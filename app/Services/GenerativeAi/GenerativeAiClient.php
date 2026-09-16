<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Client HTTP Anthropic (Messages API, JSON via outil forcé, prompt cache).
 *
 * Aucun SDK. Les tests faussent HTTP — pas d’appel réel en CI.
 *
 * @example
 * $out = app(GenerativeAiClient::class)->complete($supervisor, $user, $schema);
 */
final class GenerativeAiClient
{
    public const TOOL_NAME = 'submit_json';

    public function model(): string
    {
        $model = config('services.anthropic.model', 'claude-sonnet-5');

        return is_string($model) && $model !== '' ? $model : 'claude-sonnet-5';
    }

    /**
     * @param  array<string, mixed>  $jsonSchema
     */
    public function complete(string $supervisor, string $userMessage, array $jsonSchema): LlmResponse
    {
        $key = $this->apiKey();
        if ($key === '') {
            throw new RuntimeException('Clé Anthropic absente (ANTHROPIC_API_KEY).');
        }

        $schema = $this->normalizeSchema($jsonSchema);
        $payload = [
            'model' => $this->model(),
            'max_tokens' => 4096,
            'system' => [
                [
                    'type' => 'text',
                    'text' => $supervisor,
                    'cache_control' => ['type' => 'ephemeral'],
                ],
            ],
            'messages' => [
                ['role' => 'user', 'content' => $userMessage],
            ],
            'tools' => [
                [
                    'name' => self::TOOL_NAME,
                    'description' => 'Soumettre le JSON de conversion JDR (clés autorisées seulement).',
                    'input_schema' => $schema,
                ],
            ],
            'tool_choice' => ['type' => 'tool', 'name' => self::TOOL_NAME],
        ];

        $response = Http::withHeaders([
            'x-api-key' => $key,
            'anthropic-version' => (string) config('services.anthropic.version', '2023-06-01'),
            'content-type' => 'application/json',
        ])
            ->timeout((int) config('services.anthropic.timeout', 120))
            ->acceptJson()
            ->post($this->baseUrl().'/v1/messages', $payload);

        if (! $response->successful()) {
            throw new RuntimeException($this->httpErrorMessage($response));
        }

        return $this->parse($response);
    }

    public function apiKey(): string
    {
        $key = config('services.anthropic.api_key');

        return is_string($key) ? trim($key) : '';
    }

    public function hasApiKey(): bool
    {
        return $this->apiKey() !== '';
    }

    private function baseUrl(): string
    {
        $url = config('services.anthropic.base_url', 'https://api.anthropic.com');

        return rtrim(is_string($url) && $url !== '' ? $url : 'https://api.anthropic.com', '/');
    }

    /**
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    private function normalizeSchema(array $schema): array
    {
        if (! isset($schema['type'])) {
            $schema['type'] = 'object';
        }
        if (! isset($schema['properties']) || ! is_array($schema['properties'])) {
            $schema['properties'] = new \stdClass;
        }
        $schema['additionalProperties'] = false;

        return $schema;
    }

    private function parse(Response $response): LlmResponse
    {
        $body = $response->json();
        if (! is_array($body)) {
            throw new RuntimeException('Réponse Anthropic invalide.');
        }

        $json = $this->extractToolJson($body);
        $usage = is_array($body['usage'] ?? null) ? $body['usage'] : [];
        $model = is_string($body['model'] ?? null) ? $body['model'] : $this->model();

        return new LlmResponse(
            json: $json,
            model: $model,
            inputTokens: (int) ($usage['input_tokens'] ?? 0),
            outputTokens: (int) ($usage['output_tokens'] ?? 0),
            cacheReadTokens: (int) ($usage['cache_read_input_tokens'] ?? 0),
        );
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function extractToolJson(array $body): array
    {
        $content = $body['content'] ?? [];
        if (! is_array($content)) {
            throw new RuntimeException('Réponse Anthropic sans contenu.');
        }

        foreach ($content as $block) {
            if (! is_array($block)) {
                continue;
            }
            $type = $block['type'] ?? '';
            if ($type === 'tool_use' && ($block['name'] ?? '') === self::TOOL_NAME) {
                $input = $block['input'] ?? [];
                if (! is_array($input)) {
                    throw new RuntimeException('JSON d’outil Anthropic invalide.');
                }

                return $input;
            }
            if ($type === 'text' && is_string($block['text'] ?? null)) {
                $decoded = json_decode($block['text'], true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        throw new RuntimeException('Réponse Anthropic sans JSON structuré.');
    }

    private function httpErrorMessage(Response $response): string
    {
        $body = $response->json();
        $detail = is_array($body) ? ($body['error']['message'] ?? $body['message'] ?? null) : null;
        $suffix = is_string($detail) && $detail !== '' ? ' : '.$detail : '';

        return 'Appel Anthropic refusé (HTTP '.$response->status().')'.$suffix;
    }
}
