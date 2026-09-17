<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Services\GenerativeAi\GenerativeAiClient;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

final class GenerativeAiClientTest extends TestCase
{
    public function test_complete_parses_forced_tool_json_and_marks_explicit_cache(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::fake([
            '*anthropic.com/v1/messages' => Http::response([
                'model' => 'claude-haiku-4-5',
                'content' => [[
                    'type' => 'tool_use',
                    'name' => GenerativeAiClient::TOOL_NAME,
                    'input' => ['effect' => '1d6 Feu'],
                ]],
                'usage' => [
                    'input_tokens' => 120,
                    'output_tokens' => 40,
                    'cache_read_input_tokens' => 80,
                ],
            ], 200),
        ]);

        $response = app(GenerativeAiClient::class)->complete(
            'superviseur',
            'fiche source',
            ['type' => 'object', 'properties' => ['effect' => ['type' => 'string']]],
            'préfixe stable few-shot'
        );

        $this->assertSame(['effect' => '1d6 Feu'], $response->json);
        $this->assertSame('claude-haiku-4-5', $response->model);
        $this->assertSame(120, $response->inputTokens);
        $this->assertSame(40, $response->outputTokens);
        $this->assertSame(80, $response->cacheReadTokens);
        Http::assertSent(function ($request): bool {
            $payload = $request->data();
            $user = $payload['messages'][0]['content'] ?? [];

            return $request->hasHeader('x-api-key', 'test-key')
                && ($payload['model'] ?? null) === 'claude-haiku-4-5'
                && ($payload['tool_choice']['name'] ?? null) === GenerativeAiClient::TOOL_NAME
                && ($payload['tools'][0]['cache_control']['type'] ?? null) === 'ephemeral'
                && ($user[0]['text'] ?? null) === 'préfixe stable few-shot'
                && ($user[0]['cache_control']['type'] ?? null) === 'ephemeral'
                && ($user[1]['text'] ?? null) === 'fiche source'
                && ! isset($user[1]['cache_control'])
                && ! isset($payload['system'][0]['cache_control']);
        });
    }

    public function test_complete_omits_cache_control_when_disabled(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::fake([
            '*anthropic.com/v1/messages' => Http::response([
                'model' => 'claude-haiku-4-5',
                'content' => [[
                    'type' => 'tool_use',
                    'name' => GenerativeAiClient::TOOL_NAME,
                    'input' => ['ok' => true],
                ]],
                'usage' => ['input_tokens' => 1, 'output_tokens' => 1],
            ], 200),
        ]);

        app(GenerativeAiClient::class)->complete(
            's',
            'u',
            ['type' => 'object'],
            'préfixe',
            false
        );

        Http::assertSent(function ($request): bool {
            $payload = $request->data();
            $encoded = json_encode($payload);

            return is_string($encoded) && ! str_contains($encoded, 'cache_control');
        });
    }

    public function test_missing_api_key_throws_without_http(): void
    {
        config(['services.anthropic.api_key' => '']);
        Http::fake();
        Http::preventStrayRequests();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Clé Anthropic absente');

        app(GenerativeAiClient::class)->complete('s', 'u', ['type' => 'object']);
    }
}
