<?php

declare(strict_types=1);

namespace Tests\Feature\GenerativeAi;

use App\Services\GenerativeAi\GenerativeAiClient;
use Illuminate\Support\Facades\Http;

/**
 * Fake HTTP Anthropic (outil submit_json). Jamais d’appel réel.
 *
 * @param  array<string, mixed>  $input
 */
trait FakesAnthropicJson
{
    /**
     * @param  array<string, mixed>  $input
     */
    protected function fakeAnthropicJson(array $input, int $inputTokens = 80, int $outputTokens = 30): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::preventStrayRequests();
        Http::fake([
            '*anthropic.com/v1/messages' => Http::response([
                'model' => 'claude-sonnet-5',
                'content' => [[
                    'type' => 'tool_use',
                    'name' => GenerativeAiClient::TOOL_NAME,
                    'input' => $input,
                ]],
                'usage' => [
                    'input_tokens' => $inputTokens,
                    'output_tokens' => $outputTokens,
                    'cache_read_input_tokens' => 5,
                ],
            ], 200),
        ]);
    }
}
