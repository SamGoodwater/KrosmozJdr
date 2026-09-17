<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Models\AiGenerationRun;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Solde / usage Anthropic (API fournisseur). Échec silencieux si pas de clé.
 *
 * @example $snap = app(AnthropicUsageService::class)->snapshot(false);
 */
final class AnthropicUsageService
{
    /**
     * Tokens locaux du mois, et optionnellement l’usage org Anthropic.
     *
     * @param  bool  $includeRemote  false = pas d’HTTP fournisseur (réponse locale, modal Sources).
     * @return array{
     *     available: bool,
     *     reason: string|null,
     *     message: string,
     *     input_tokens: int|null,
     *     output_tokens: int|null,
     *     cost_usd: float|null,
     *     remaining_credits_usd: float|null,
     *     remaining_hint: string|null,
     *     local_input_tokens: int,
     *     local_output_tokens: int,
     *     local_runs: int,
     *     has_api_key: bool,
     *     model: string
     * }
     */
    public function snapshot(bool $includeRemote = true): array
    {
        $client = app(GenerativeAiClient::class);
        $local = $this->localMonth();
        $base = [
            'available' => false,
            'reason' => $client->hasApiKey() ? null : 'no_key',
            'message' => '',
            'input_tokens' => null,
            'output_tokens' => null,
            'cost_usd' => null,
            'remaining_credits_usd' => null,
            'remaining_hint' => null,
            'local_input_tokens' => $local['input_tokens'],
            'local_output_tokens' => $local['output_tokens'],
            'local_runs' => $local['runs'],
            'has_api_key' => $client->hasApiKey(),
            'model' => $client->model(),
        ];

        $remote = [
            'available' => false,
            'reason' => $client->hasApiKey() ? null : 'no_key',
            'input_tokens' => null,
            'output_tokens' => null,
            'cost_usd' => null,
            'remaining_credits_usd' => null,
        ];
        if ($includeRemote && $client->hasApiKey()) {
            $remote = Cache::remember('ia.anthropic.usage.remote.v1', 120, fn (): array => $this->fetchRemote($client));
        }

        $merged = [
            ...$base,
            ...$remote,
            'local_input_tokens' => $local['input_tokens'],
            'local_output_tokens' => $local['output_tokens'],
            'local_runs' => $local['runs'],
            'has_api_key' => $client->hasApiKey(),
            'model' => $client->model(),
        ];
        $merged['remaining_hint'] = app(CostEstimator::class)
            ->remainingHint($merged['remaining_credits_usd'] ?? null, $client->model());
        $merged['message'] = $this->composeMessage($merged);

        return $merged;
    }

    /**
     * @return array{input_tokens: int, output_tokens: int, runs: int}
     */
    public function localMonth(): array
    {
        $empty = [
            'input_tokens' => 0,
            'output_tokens' => 0,
            'runs' => 0,
        ];
        if (! Schema::hasTable((new AiGenerationRun)->getTable())) {
            return $empty;
        }

        $row = AiGenerationRun::query()
            ->where('status', AiGenerationRun::STATUS_SUCCESS)
            ->where('created_at', '>=', now()->startOfMonth())
            ->selectRaw(
                'COALESCE(SUM(input_tokens), 0) as input_tokens, COALESCE(SUM(output_tokens), 0) as output_tokens, COUNT(*) as runs'
            )
            ->first();

        return [
            'input_tokens' => (int) ($row?->input_tokens ?? 0),
            'output_tokens' => (int) ($row?->output_tokens ?? 0),
            'runs' => (int) ($row?->runs ?? 0),
        ];
    }

    /**
     * @return array{
     *     available: bool,
     *     reason: string|null,
     *     input_tokens: int|null,
     *     output_tokens: int|null,
     *     cost_usd: float|null,
     *     remaining_credits_usd: float|null
     * }
     */
    private function fetchRemote(GenerativeAiClient $client): array
    {
        $empty = [
            'available' => false,
            'reason' => 'http_error',
            'input_tokens' => null,
            'output_tokens' => null,
            'cost_usd' => null,
            'remaining_credits_usd' => null,
        ];

        try {
            $base = rtrim((string) config('services.anthropic.base_url', 'https://api.anthropic.com'), '/');
            $startingAt = now()->startOfMonth()->toIso8601String();
            $response = Http::withHeaders([
                'x-api-key' => $client->apiKey(),
                'anthropic-version' => (string) config('services.anthropic.version', '2023-06-01'),
                'content-type' => 'application/json',
            ])
                ->timeout(3)
                ->acceptJson()
                ->get($base.'/v1/organizations/usage', [
                    'starting_at' => $startingAt,
                ]);

            if (! $response->successful()) {
                return $empty;
            }

            $usage = $this->extractUsage($response->json());

            return [
                'available' => true,
                'reason' => null,
                'input_tokens' => $usage['input_tokens'],
                'output_tokens' => $usage['output_tokens'],
                'cost_usd' => $usage['cost_usd'],
                'remaining_credits_usd' => $usage['remaining_credits_usd'],
            ];
        } catch (\Throwable $exception) {
            Log::notice('Usage Anthropic indisponible', ['error' => $exception->getMessage()]);

            return $empty;
        }
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
     * @param  array{
     *     available: bool,
     *     reason: string|null,
     *     input_tokens: int|null,
     *     output_tokens: int|null,
     *     cost_usd: float|null,
     *     remaining_credits_usd: float|null,
     *     remaining_hint: string|null,
     *     local_input_tokens: int,
     *     local_output_tokens: int,
     *     local_runs: int,
     *     has_api_key: bool
     * }  $usage
     */
    private function composeMessage(array $usage): string
    {
        $parts = [];
        $parts[] = sprintf(
            'Cette app (mois) : %s entrée · %s sortie · %s conversion(s)',
            number_format((int) $usage['local_input_tokens'], 0, ',', ' '),
            number_format((int) $usage['local_output_tokens'], 0, ',', ' '),
            number_format((int) $usage['local_runs'], 0, ',', ' ')
        );

        if ($usage['available'] === true) {
            if ($usage['input_tokens'] !== null || $usage['output_tokens'] !== null) {
                $parts[] = sprintf(
                    'Anthropic : entrée %s · sortie %s',
                    number_format((int) ($usage['input_tokens'] ?? 0), 0, ',', ' '),
                    number_format((int) ($usage['output_tokens'] ?? 0), 0, ',', ' ')
                );
            }
            if ($usage['cost_usd'] !== null) {
                $parts[] = 'coût ~ '.$this->usd($usage['cost_usd']);
            }
        } elseif (($usage['reason'] ?? null) === 'no_key') {
            $parts[] = 'Aucune clé Anthropic : solde fournisseur indisponible';
        } elseif (($usage['reason'] ?? null) === 'http_error') {
            $parts[] = 'Usage Anthropic indisponible. La génération reste possible';
        }

        if ($usage['remaining_credits_usd'] !== null) {
            $credit = 'crédit restant '.$this->usd($usage['remaining_credits_usd']);
            if (is_string($usage['remaining_hint'] ?? null) && $usage['remaining_hint'] !== '') {
                $credit .= ' '.$usage['remaining_hint'];
            }
            $parts[] = $credit;
        }

        return implode(' — ', $parts).'.';
    }

    private function usd(float $value): string
    {
        return number_format($value, 2, ',', ' ').' $';
    }
}
