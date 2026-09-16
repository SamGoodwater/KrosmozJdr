<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Models\AiGenerationRun;
use App\Services\GenerativeAi\AnthropicUsageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AnthropicUsageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_snapshot_includes_local_month_tokens_without_api_key(): void
    {
        config(['services.anthropic.api_key' => '']);

        AiGenerationRun::query()->create([
            'action' => 'encounter',
            'entity_type' => 'monster',
            'entity_id' => 1,
            'status' => AiGenerationRun::STATUS_SUCCESS,
            'input_tokens' => 120,
            'output_tokens' => 40,
        ]);
        AiGenerationRun::query()->create([
            'action' => 'spell',
            'entity_type' => 'spell',
            'entity_id' => 2,
            'status' => AiGenerationRun::STATUS_FAILED,
            'input_tokens' => 999,
            'output_tokens' => 999,
        ]);

        $snap = app(AnthropicUsageService::class)->snapshot();

        $this->assertFalse($snap['available']);
        $this->assertFalse($snap['has_api_key']);
        $this->assertSame(120, $snap['local_input_tokens']);
        $this->assertSame(40, $snap['local_output_tokens']);
        $this->assertSame(1, $snap['local_runs']);
        $this->assertNull($snap['remaining_credits_usd']);
        $this->assertStringContainsString('Cette app (mois)', $snap['message']);
        $this->assertStringContainsString('120', $snap['message']);
    }
}
