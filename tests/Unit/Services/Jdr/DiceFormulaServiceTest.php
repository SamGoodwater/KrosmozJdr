<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Jdr;

use App\Services\Jdr\DiceFormulaService;
use App\Services\Jdr\DiceNotationService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @see DiceFormulaService
 */
class DiceFormulaServiceTest extends TestCase
{
    private DiceFormulaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DiceFormulaService(new DiceNotationService);
    }

    public function test_empty_formula_is_not_recognized(): void
    {
        $result = $this->service->analyze('');
        $this->assertFalse($result->isValid);
        $this->assertFalse($result->isRecognized);
        $this->assertNull($result->error);
    }

    public function test_lone_number_is_valid_but_not_recognized(): void
    {
        $result = $this->service->analyze('12');
        $this->assertTrue($result->isValid);
        $this->assertFalse($result->isRecognized);
        $this->assertSame(12, $result->min);
        $this->assertSame(12, $result->max);
        $this->assertSame(12, $result->average);
    }

    public function test_bare_dice_and_counted_dice(): void
    {
        $d12 = $this->service->analyze('d12');
        $this->assertTrue($d12->isRecognized);
        $this->assertSame(1, $d12->min);
        $this->assertSame(12, $d12->max);
        $this->assertSame(6.5, $d12->average);

        $threeD8 = $this->service->analyze('3d8');
        $this->assertTrue($threeD8->isRecognized);
        $this->assertSame(3, $threeD8->min);
        $this->assertSame(24, $threeD8->max);
        $this->assertSame(13.5, $threeD8->average);
    }

    public function test_range_stats_and_exact_dice_equivalent(): void
    {
        $result = $this->service->analyze('[2-6]');
        $this->assertTrue($result->isRecognized);
        $this->assertSame(2, $result->min);
        $this->assertSame(6, $result->max);
        $this->assertSame(4, $result->average);
        $this->assertSame(['[2-6] = 1d5+1'], $result->rangeEquivalents);
    }

    public function test_numeric_operations_are_recognized(): void
    {
        $hp = $this->service->analyze('50-17');
        $this->assertTrue($hp->isValid);
        $this->assertTrue($hp->isRecognized);
        $this->assertSame(33, $hp->min);
        $this->assertSame(33, $hp->max);
        $this->assertSame(33, $hp->average);

        $ops = $this->service->analyze('1+4*7');
        $this->assertTrue($ops->isRecognized);
        $this->assertSame(29, $ops->min);
        $this->assertSame(29, $ops->max);
        $this->assertSame(29, $ops->average);

        $rolled = $this->service->roll('50-17');
        $this->assertTrue($rolled->isValid);
        $this->assertSame(33, $rolled->result);
    }

    public function test_operators_aliases_and_priority(): void
    {
        $result = $this->service->analyze('2d6+3');
        $this->assertTrue($result->isRecognized);
        $this->assertSame(5, $result->min);
        $this->assertSame(15, $result->max);
        $this->assertSame(10, $result->average);

        $times = $this->service->analyze('2d6 x 2');
        $this->assertSame(4, $times->min);
        $this->assertSame(24, $times->max);

        $div = $this->service->analyze('4d6÷2');
        $this->assertSame(2, $div->min);
        $this->assertSame(12, $div->max);
    }

    public function test_decimal_comma_and_dot(): void
    {
        $dot = $this->service->analyze('1.5+d4');
        $this->assertTrue($dot->isRecognized);
        $this->assertSame(2.5, $dot->min);
        $this->assertSame(5.5, $dot->max);

        $comma = $this->service->analyze('1,5+d4');
        $this->assertSame($dot->min, $comma->min);
        $this->assertSame($dot->max, $comma->max);
    }

    public function test_combined_formula_with_range(): void
    {
        $result = $this->service->analyze('2d6+[2-6]');
        $this->assertTrue($result->isRecognized);
        $this->assertSame(4, $result->min);
        $this->assertSame(18, $result->max);
        $this->assertSame(['[2-6] = 1d5+1'], $result->rangeEquivalents);
    }

    public function test_roll_stays_within_bounds(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $rolled = $this->service->roll('[2-6]');
            $this->assertTrue($rolled->isValid);
            $this->assertGreaterThanOrEqual(2, $rolled->result);
            $this->assertLessThanOrEqual(6, $rolled->result);
        }

        $dice = $this->service->roll('1d4');
        $this->assertTrue($dice->isValid);
        $this->assertGreaterThanOrEqual(1, $dice->result);
        $this->assertLessThanOrEqual(4, $dice->result);
    }

    #[DataProvider('maliciousInputs')]
    public function test_rejects_malicious_or_invalid_input(string $input): void
    {
        $result = $this->service->analyze($input);
        $this->assertFalse($result->isValid);
        $this->assertFalse($result->isRecognized);
        $this->assertNull($result->min);

        $rolled = $this->service->roll($input);
        $this->assertFalse($rolled->isValid);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function maliciousInputs(): array
    {
        return [
            'php' => ['<?php echo 1;'],
            'js' => ['<script>alert(1)</script>'],
            'eval' => ['2d6; system("id")'],
            'concat' => ["2d6'.passthru('id').'"],
            'scientific' => ['1e999'],
            'too_many_dice' => ['99d6'],
            'too_many_faces' => ['1d99999'],
            'parentheses' => ['(2d6)+3'],
            'letters' => ['foo'],
            'sql' => ["1'; DROP TABLE users;--"],
            'overflow_range' => ['[1-99999]'],
            'trailing_op' => ['2d6+'],
        ];
    }
}
