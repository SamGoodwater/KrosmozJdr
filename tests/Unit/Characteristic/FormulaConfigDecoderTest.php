<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\FormulaConfigDecoder;
use Tests\TestCase;

class FormulaConfigDecoderTest extends TestCase
{
    public function test_decode_empty_returns_formula_type(): void
    {
        $decoded = FormulaConfigDecoder::decode(null);
        $this->assertSame('formula', $decoded['type']);
        $this->assertSame('', $decoded['expression']);

        $decoded = FormulaConfigDecoder::decode('');
        $this->assertSame('formula', $decoded['type']);
    }

    public function test_decode_simple_formula(): void
    {
        $decoded = FormulaConfigDecoder::decode('[vitality]*10+[level]*2');
        $this->assertSame('formula', $decoded['type']);
        $this->assertSame('[vitality]*10+[level]*2', $decoded['expression']);
    }

    public function test_decode_table_json(): void
    {
        $json = '{"characteristic":"level","1":0,"7":2,"14":4}';
        $decoded = FormulaConfigDecoder::decode($json);
        $this->assertSame('table', $decoded['type']);
        $this->assertSame('level', $decoded['characteristic']);
        $this->assertCount(3, $decoded['entries']);
        $this->assertSame(1, $decoded['entries'][0]['from']);
        $this->assertSame(0.0, $decoded['entries'][0]['value']);
        $this->assertSame(7, $decoded['entries'][1]['from']);
        $this->assertSame(2.0, $decoded['entries'][1]['value']);
        $this->assertSame(14, $decoded['entries'][2]['from']);
        $this->assertSame(4.0, $decoded['entries'][2]['value']);
    }

    public function test_encode_formula(): void
    {
        $encoded = FormulaConfigDecoder::encode(['type' => 'formula', 'expression' => '[level]*2']);
        $this->assertSame('[level]*2', $encoded);
    }

    public function test_encode_table(): void
    {
        $decoded = [
            'type' => 'table',
            'characteristic' => 'level',
            'entries' => [
                ['from' => 1, 'value' => 0],
                ['from' => 7, 'value' => 2],
                ['from' => 14, 'value' => 4],
            ],
        ];
        $encoded = FormulaConfigDecoder::encode($decoded);
        $this->assertStringContainsString('"characteristic":"level"', $encoded);
        $this->assertStringContainsString('"1":0', $encoded);
        $this->assertStringContainsString('"7":2', $encoded);
        $this->assertStringContainsString('"14":4', $encoded);
    }

    public function test_is_table(): void
    {
        $this->assertFalse(FormulaConfigDecoder::isTable(null));
        $this->assertFalse(FormulaConfigDecoder::isTable('[level]*2'));
        $this->assertTrue(FormulaConfigDecoder::isTable('{"characteristic":"level","1":0}'));
    }
}
