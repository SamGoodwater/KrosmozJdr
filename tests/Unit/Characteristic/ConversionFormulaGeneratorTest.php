<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Conversion\ConversionFormulaGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Services\Characteristic\Conversion\ConversionFormulaGenerator
 */
class ConversionFormulaGeneratorTest extends TestCase
{
    private ConversionFormulaGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new ConversionFormulaGenerator();
    }

    public function test_pairs_from_samples_matches_by_level(): void
    {
        $dofus = ['1' => 10, '5' => 50, '10' => 200];
        $krosmoz = [1 => 1, 5 => 5, 10 => 10];
        $pairs = $this->generator->pairsFromSamples($dofus, $krosmoz);
        $this->assertCount(3, $pairs);
        $this->assertSame(10.0, $pairs[0]['d']);
        $this->assertSame(1.0, $pairs[0]['k']);
        $this->assertSame(200.0, $pairs[2]['d']);
        $this->assertSame(10.0, $pairs[2]['k']);
    }

    public function test_generate_table_from_pairs_produces_valid_json(): void
    {
        $pairs = [['d' => 10.0, 'k' => 1.0], ['d' => 200.0, 'k' => 20.0]];
        $table = $this->generator->generateTableFromPairs($pairs);
        $decoded = json_decode($table, true);
        $this->assertSame('d', $decoded['characteristic']);
        $this->assertSame(1, $decoded['10']);
        $this->assertSame(20, $decoded['200']);
    }

    public function test_fit_power_returns_formula_for_positive_data(): void
    {
        $pairs = [
            ['d' => 10.0, 'k' => 5.0],
            ['d' => 100.0, 'k' => 20.0],
        ];
        $result = $this->generator->fitPower($pairs);
        $this->assertNotNull($result);
        $this->assertArrayHasKey('formula', $result);
        $this->assertStringContainsString('pow([d]', $result['formula']);
        $this->assertGreaterThanOrEqual(0, $result['r2']);
    }

    public function test_fit_shifted_power_returns_formula(): void
    {
        $pairs = [
            ['d' => 50.0, 'k' => 8.0],
            ['d' => 500.0, 'k' => 20.0],
            ['d' => 1200.0, 'k' => 30.0],
        ];
        $result = $this->generator->fitShiftedPower($pairs);
        $this->assertNotNull($result);
        $this->assertStringContainsString('pow(([d]', $result['formula']);
        $this->assertArrayHasKey('f', $result);
    }

    public function test_fit_linear_returns_formula(): void
    {
        $pairs = [
            ['d' => 0.0, 'k' => 0.0],
            ['d' => 10.0, 'k' => 1.0],
            ['d' => 200.0, 'k' => 20.0],
        ];
        $result = $this->generator->fitLinear($pairs);
        $this->assertNotNull($result);
        $this->assertStringContainsString('[d]', $result['formula']);
        $this->assertGreaterThanOrEqual(0, $result['r2']);
    }

    public function test_suggest_formulas_returns_table_and_suggestions(): void
    {
        $dofus = ['1' => 1, '10' => 100, '200' => 200];
        $krosmoz = ['1' => 1, '10' => 10, '200' => 20];
        $out = $this->generator->suggestFormulas($dofus, $krosmoz);
        $this->assertArrayHasKey('table', $out);
        $this->assertArrayHasKey('linear', $out);
        $this->assertArrayHasKey('power', $out);
        $this->assertArrayHasKey('shifted_power', $out);
        $this->assertStringContainsString('"characteristic":"d"', $out['table']);
    }
}
