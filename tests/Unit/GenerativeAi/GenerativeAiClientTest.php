<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Services\GenerativeAi\GenerativeAiClient;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

final class GenerativeAiClientTest extends TestCase
{
    public function test_complete_parses_forced_tool_json_and_usage(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::fake([
            '*anthropic.com/v1/messages' => Http::response([
                'model' => 'claude-sonnet-5',
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
            'user',
            ['type' => 'object', 'properties' => ['effect' => ['type' => 'string']]]
        );

        $this->assertSame(['effect' => '1d6 Feu'], $response->json);
        $this->assertSame('claude-sonnet-5', $response->model);
        $this->assertSame(120, $response->inputTokens);
        $this->assertSame(40, $response->outputTokens);
        $this->assertSame(80, $response->cacheReadTokens);
        Http::assertSent(function ($request): bool {
            $payload = $request->data();

            return $request->hasHeader('x-api-key', 'test-key')
                && ($payload['tool_choice']['name'] ?? null) === GenerativeAiClient::TOOL_NAME;
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
