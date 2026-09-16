<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Solde / usage Anthropic (API fournisseur). Échec silencieux si pas de clé.
 *
 * @example $snap = app(AnthropicUsageService::class)->snapshot();
 */
final class AnthropicUsageService
{
    /**
     * @return array{
     *     available: bool,
     *     reason: string|null,
     *     message: string,
     *     input_tokens: int|null,
     *     output_tokens: int|null,
     *     cost_usd: float|null,
     *     remaining_credits_usd: float|null,
     *     model: string
     * }
     */
    public function snapshot(): array
    {
        $client = app(GenerativeAiClient::class);
        $empty = [
            'available' => false,
            'reason' => 'no_key',
            'message' => 'Aucune clé Anthropic : solde indisponible.',
            'input_tokens' => null,
            'output_tokens' => null,
            'cost_usd' => null,
            'remaining_credits_usd' => null,
            'model' => $client->model(),
        ];

        if (! $client->hasApiKey()) {
            return $empty;
        }

        return Cache::remember('ia.anthropic.usage', 120, function () use ($client, $empty): array {
            try {
                $base = rtrim((string) config('services.anthropic.base_url', 'https://api.anthropic.com'), '/');
                $startingAt = now()->startOfMonth()->toIso8601String();
                $response = Http::withHeaders([
                    'x-api-key' => $client->apiKey(),
                    'anthropic-version' => (string) config('services.anthropic.version', '2023-06-01'),
                    'content-type' => 'application/json',
                ])
                    ->timeout(15)
                    ->acceptJson()
                    ->get($base.'/v1/organizations/usage', [
                        'starting_at' => $startingAt,
                    ]);

                if (! $response->successful()) {
                    return [
                        ...$empty,
                        'reason' => 'http_error',
                        'message' => 'Usage Anthropic indisponible ('.$response->status().'). La génération reste possible.',
                    ];
                }

                $body = $response->json();
                $usage = $this->extractUsage($body);

                return [
                    'available' => true,
                    'reason' => null,
                    'message' => $this->formatMessage($usage),
                    'input_tokens' => $usage['input_tokens'],
                    'output_tokens' => $usage['output_tokens'],
                    'cost_usd' => $usage['cost_usd'],
                    'remaining_credits_usd' => $usage['remaining_credits_usd'],
                    'model' => $client->model(),
                ];
            } catch (\Throwable $exception) {
                Log::notice('Usage Anthropic indisponible', ['error' => $exception->getMessage()]);

                return [
                    ...$empty,
                    'reason' => 'http_error',
                    'message' => 'Usage Anthropic indisponible. La génération reste possible.',
                ];
            }
        });
    }

    /**
     * @return array{input_tokens: int|null, output_tokens: int|null, cost_usd: float|null, remaining_credits_usd: float|null}
     */
    private function extractUsage(mixed $body): array
    {
        $out = [
            'input_tokens' => null,
            'output_tokens' => null,
            'cost_usd' => null,
            'remaining_credits_usd' => null,
        ];
        if (! is_array($body)) {
            return $out;
        }

        $data = is_array($body['data'] ?? null) ? $body['data'] : [$body];
        $input = 0;
        $output = 0;
        $cost = 0.0;
        $hasTokens = false;
        $hasCost = false;

        foreach ($data as $row) {
            if (! is_array($row)) {
                continue;
            }
            if (isset($row['input_tokens']) && is_numeric($row['input_tokens'])) {
                $input += (int) $row['input_tokens'];
                $hasTokens = true;
            }
            if (isset($row['uncached_input_tokens']) && is_numeric($row['uncached_input_tokens'])) {
                $input += (int) $row['uncached_input_tokens'];
                $hasTokens = true;
            }
            if (isset($row['output_tokens']) && is_numeric($row['output_tokens'])) {
                $output += (int) $row['output_tokens'];
                $hasTokens = true;
            }
            foreach (['cost_usd', 'amount_usd', 'total_cost'] as $costKey) {
                if (isset($row[$costKey]) && is_numeric($row[$costKey])) {
                    $cost += (float) $row[$costKey];
                    $hasCost = true;
                }
            }
            if (isset($row['results']) && is_array($row['results'])) {
                $nested = $this->extractUsage(['data' => $row['results']]);
                if ($nested['input_tokens'] !== null) {
                    $input += $nested['input_tokens'];
                    $hasTokens = true;
                }
                if ($nested['output_tokens'] !== null) {
                    $output += $nested['output_tokens'];
                    $hasTokens = true;
                }
                if ($nested['cost_usd'] !== null) {
                    $cost += $nested['cost_usd'];
                    $hasCost = true;
                }
            }
        }

        $remaining = $body['remaining_credits_usd']
            ?? $body['credits_remaining']
            ?? $body['remaining_balance']
            ?? null;

        return [
            'input_tokens' => $hasTokens ? $input : null,
            'output_tokens' => $hasTokens ? $output : null,
            'cost_usd' => $hasCost ? round($cost, 4) : (is_numeric($body['cost_usd'] ?? null) ? (float) $body['cost_usd'] : null),
            'remaining_credits_usd' => is_numeric($remaining) ? (float) $remaining : null,
        ];
    }

    /**
     * @param  array{input_tokens: int|null, output_tokens: int|null, cost_usd: float|null, remaining_credits_usd: float|null}  $usage
     */
    private function formatMessage(array $usage): string
    {
        $parts = ['Usage Anthropic (mois en cours)'];
        if ($usage['input_tokens'] !== null || $usage['output_tokens'] !== null) {
            $parts[] = sprintf(
                'entrée %s · sortie %s tokens',
                number_format((int) ($usage['input_tokens'] ?? 0), 0, ',', ' '),
                number_format((int) ($usage['output_tokens'] ?? 0), 0, ',', ' ')
            );
        }
        if ($usage['cost_usd'] !== null) {
            $parts[] = 'coût ~ '.$this->usd($usage['cost_usd']);
        }
        if ($usage['remaining_credits_usd'] !== null) {
            $parts[] = 'crédit restant '.$this->usd($usage['remaining_credits_usd']);
        }

        return implode(' — ', $parts).'.';
    }

    private function usd(float $value): string
    {
        return number_format($value, 2, ',', ' ').' $';
    }
}
