<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Item\ApplyDraftItemDescriptions;
use Tests\TestCase;

final class DraftItemDescriptionCatalogTest extends TestCase
{
    public function test_draft_item_descriptions_stay_short_and_in_tu(): void
    {
        $service = app(ApplyDraftItemDescriptions::class);
        $grid = ApplyDraftItemDescriptions::GRID_DESCRIPTION;

        $this->assertDoesNotMatchRegularExpression('/\bvous\b/iu', $grid);
        $this->assertStringNotContainsString('grille', mb_strtolower($grid));
        $this->assertStringNotContainsString('publication', mb_strtolower($grid));

        $descriptions = $service->descriptions();
        $this->assertGreaterThan(100, count($descriptions));

        foreach ($descriptions as $dofusdbId => $description) {
            $label = (string) $dofusdbId;
            $this->assertLessThanOrEqual(450, mb_strlen($description), $label);
            $this->assertDoesNotMatchRegularExpression('/\bvous\b/iu', $description, $label);
            $this->assertStringNotContainsString("'", $description, $label);
            $this->assertDoesNotMatchRegularExpression('/·[^·]{0,12}·/u', $description, $label);
        }
    }
}
