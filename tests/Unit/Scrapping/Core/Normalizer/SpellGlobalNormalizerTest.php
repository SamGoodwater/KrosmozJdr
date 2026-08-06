<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Normalizer;

use App\Services\Scrapping\Core\Normalizer\SpellGlobalNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SpellGlobalNormalizerTest extends TestCase
{
    #[DataProvider('targetTypeProvider')]
    public function test_it_normalizes_global_metadata(string $triggers, string $expectedTargetType): void
    {
        $result = (new SpellGlobalNormalizer)->build([
            'levels' => [[
                'castInLine' => true,
                'castInDiagonal' => true,
                'maxStack' => 14,
                'globalCooldown' => -2,
                'minPlayerLevel' => 100,
                'effects' => [['triggers' => $triggers]],
            ]],
        ]);

        self::assertTrue($result['castInLine']);
        self::assertTrue($result['castInDiagonal']);
        self::assertSame(10, $result['maxStack']);
        self::assertSame(0, $result['globalCooldown']);
        self::assertSame(100, $result['minPlayerLevel']);
        self::assertSame($expectedTargetType, $result['targetType']);
    }

    /** @return iterable<string, array{string, string}> */
    public static function targetTypeProvider(): iterable
    {
        yield 'direct' => ['', 'direct'];
        yield 'piège' => ['P', 'trap'];
        yield 'glyphe' => ['G', 'glyph'];
        yield 'priorité piège' => ['GP', 'trap'];
    }

    public function test_it_provides_safe_defaults_without_level(): void
    {
        $result = (new SpellGlobalNormalizer)->build([]);

        self::assertFalse($result['castInLine']);
        self::assertFalse($result['castInDiagonal']);
        self::assertNull($result['targetType']);
        self::assertSame(0, $result['maxStack']);
        self::assertSame(0, $result['globalCooldown']);
    }
}
