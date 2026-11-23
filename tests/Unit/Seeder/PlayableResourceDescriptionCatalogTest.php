<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Resource\ApplyPlayableResourceDescriptions;
use Tests\TestCase;

final class PlayableResourceDescriptionCatalogTest extends TestCase
{
    public function test_playable_resource_descriptions_are_short_and_in_tu(): void
    {
        $descriptions = app(ApplyPlayableResourceDescriptions::class)->descriptions();

        $this->assertCount(365, $descriptions);

        foreach ($descriptions as $dofusdbId => $description) {
            $label = (string) $dofusdbId;
            $this->assertNotSame('', $label);
            $this->assertLessThanOrEqual(400, mb_strlen($description), $label);
            $this->assertDoesNotMatchRegularExpression('/\bvous\b/iu', $description, $label);
            $this->assertStringNotContainsString("'", $description, $label);
            $this->assertDoesNotMatchRegularExpression('/·[^·]{0,12}·/u', $description, $label);
        }
    }
}
