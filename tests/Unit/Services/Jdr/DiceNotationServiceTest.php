<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Jdr;

use App\Services\Jdr\DiceNotationService;
use PHPUnit\Framework\TestCase;

/**
 * Tests du service de conversion valeur(s) → notation ndX / ndX+y.
 *
 * @see DiceNotationService
 * @see docs/50-Fonctionnalités/Scrapping/DICE_NOTATION_SERVICE.md
 */
class DiceNotationServiceTest extends TestCase
{
    private DiceNotationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DiceNotationService();
    }

    public function test_single_value_returns_ndX_or_ndX_plus_y(): void
    {
        $result = $this->service->toDiceNotation(11);
        $this->assertMatchesRegularExpression('/^\d+d\d+(?:\+\d+)?$/', $result);
    }

    public function test_close_range_returns_ndX_plus_y(): void
    {
        $result = $this->service->toDiceNotation(11, 11.4);
        $this->assertStringContainsString('+', $result, 'Écart < 5 % doit privilégier ndX+y');
    }

    public function test_wide_range_returns_ndX_only(): void
    {
        $result = $this->service->toDiceNotation(5, 20);
        $this->assertMatchesRegularExpression('/^\d+d\d+$/', $result);
        $this->assertStringNotContainsString('+', $result, 'Écart > 30 % doit retourner ndX sans y');
    }

    public function test_medium_range_returns_ndX_only(): void
    {
        $result = $this->service->toDiceNotation(4, 12);
        $this->assertMatchesRegularExpression('/^\d+d\d+$/', $result);
    }

    public function test_zero_min_handled(): void
    {
        $result = $this->service->toDiceNotation(0, 10);
        $this->assertNotEmpty($result);
        $this->assertMatchesRegularExpression('/^\d+d\d+(?:\+\d+)?$/', $result);
    }

    public function test_null_max_treated_as_single_value(): void
    {
        $single = $this->service->toDiceNotation(7);
        $withNull = $this->service->toDiceNotation(7, null);
        $this->assertSame($single, $withNull);
    }
}
