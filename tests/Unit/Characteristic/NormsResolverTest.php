<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristic\Norms\NormsResolver;
use PHPUnit\Framework\TestCase;

final class NormsResolverTest extends TestCase
{
    public function test_resolves_power_and_level_offsets_like_front_reader(): void
    {
        $grid = [
            'very_weak' => array_fill(0, 20, 1),
            'weak' => array_fill(0, 20, 2),
            'neutral' => range(1, 20),
            'strong' => array_fill(0, 20, 4),
            'very_strong' => array_fill(0, 20, 5),
        ];

        $resolver = new NormsResolver();

        $this->assertSame(10, $resolver->resolve($grid, 10));
        $this->assertSame(4, $resolver->resolve($grid, 10, 2, [
            ['target' => 'power', 'modifier' => 1],
        ]));
        $this->assertSame(12, $resolver->resolve($grid, 10, 2, [
            ['target' => 'level', 'modifier' => 2],
        ]));
    }

    public function test_compare_returns_delta(): void
    {
        $grid = ['neutral' => array_fill(0, 20, 5)];
        $result = (new NormsResolver())->compare(8, $grid, 1);

        $this->assertSame(5, $result['value']);
        $this->assertSame(3, $result['delta']);
        $this->assertFalse($result['in_band']);
    }
}
