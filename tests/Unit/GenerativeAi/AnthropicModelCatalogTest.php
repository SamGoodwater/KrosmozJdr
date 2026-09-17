<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Services\GenerativeAi\AnthropicModelCatalog;
use PHPUnit\Framework\TestCase;

final class AnthropicModelCatalogTest extends TestCase
{
    public function test_haiku_is_default_and_allowlist_rejects_unknown(): void
    {
        $this->assertSame('claude-haiku-4-5', AnthropicModelCatalog::DEFAULT);
        $this->assertTrue(AnthropicModelCatalog::isAllowed('claude-haiku-4-5'));
        $this->assertTrue(AnthropicModelCatalog::isAllowed('claude-sonnet-5'));
        $this->assertTrue(AnthropicModelCatalog::isAllowed('claude-opus-5'));
        $this->assertFalse(AnthropicModelCatalog::isAllowed('gpt-4.1'));
        $this->assertSame('claude-haiku-4-5', AnthropicModelCatalog::resolve(null));
        $this->assertSame('claude-haiku-4-5', AnthropicModelCatalog::resolve('unknown'));
        $this->assertSame('claude-sonnet-5', AnthropicModelCatalog::resolve('claude-sonnet-5'));
        $this->assertSame(0.5, AnthropicModelCatalog::costFactorVersusSonnet('claude-haiku-4-5'));
        $this->assertSame(1.0, AnthropicModelCatalog::costFactorVersusSonnet('claude-sonnet-5'));
        $this->assertCount(3, AnthropicModelCatalog::choices());
    }
}
