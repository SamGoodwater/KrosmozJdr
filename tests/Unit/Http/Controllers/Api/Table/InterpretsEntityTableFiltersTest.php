<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers\Api\Table;

use App\Http\Controllers\Api\Table\InterpretsEntityTableFilters;
use Tests\TestCase;

class InterpretsEntityTableFiltersTest extends TestCase
{
    private function subject(): object
    {
        return new class
        {
            use InterpretsEntityTableFilters;

            /**
             * @return list<string>
             */
            public function list(mixed $raw): array
            {
                return $this->normalizeFilterList($raw);
            }

            /**
             * @param  array<string, mixed>  $filters
             */
            public function has(array $filters, string $key): bool
            {
                return $this->hasFilterValue($filters, $key);
            }
        };
    }

    public function test_normalize_filter_list_accepts_csv_and_arrays(): void
    {
        $s = $this->subject();
        $this->assertSame(['5', '12'], $s->list('5,12'));
        $this->assertSame(['5', '12'], $s->list(['5', '12']));
        $this->assertSame(['5'], $s->list('5'));
        $this->assertSame([], $s->list(''));
        $this->assertSame([], $s->list([]));
        $this->assertSame(['a', 'b'], $s->list(' a, b '));
    }

    public function test_has_filter_value_ignores_empty_lists(): void
    {
        $s = $this->subject();
        $this->assertFalse($s->has([], 'level'));
        $this->assertFalse($s->has(['level' => ''], 'level'));
        $this->assertFalse($s->has(['level' => []], 'level'));
        $this->assertTrue($s->has(['level' => '5'], 'level'));
        $this->assertTrue($s->has(['level' => ['5', '12']], 'level'));
    }
}
