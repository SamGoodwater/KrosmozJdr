<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\RulesImportSlugHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RulesImportSlugHelperTest extends TestCase
{
    #[DataProvider('sectionNumberMatches')]
    public function test_section_slug_matches_toc_number(string $slug, string $number, bool $expected): void
    {
        $this->assertSame(
            $expected,
            RulesImportSlugHelper::sectionSlugMatchesNumber($slug, $number),
        );
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: bool}>
     */
    public static function sectionNumberMatches(): array
    {
        return [
            'exact number only' => ['regle-1-2-3', '1.2.3', true],
            'number plus title' => ['regle-1-2-3-concept-general', '1.2.3', true],
            'renamed title' => ['regle-1-2-3-concept-revise', '1.2.3', true],
            'chunk suffix' => ['regle-1-2-3-concept-general-extra', '1.2.3', true],
            'sibling 1.2.30 is not 1.2.3' => ['regle-1-2-30-autre', '1.2.3', false],
            'parent 1.2 is not 1.2.3' => ['regle-1-2-combat', '1.2.3', false],
            'other number' => ['regle-2-3-4-titre', '1.2.3', false],
            'empty number' => ['regle-1-2-3-titre', '', false],
        ];
    }
}
