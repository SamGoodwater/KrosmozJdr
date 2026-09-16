<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Services\GenerativeAi\CreationGuideCatalog;
use Tests\TestCase;

final class CreationGuideCatalogTest extends TestCase
{
    public function test_loads_seven_conversion_guides_with_five_blocks(): void
    {
        $catalog = new CreationGuideCatalog;
        $guides = $catalog->all();

        foreach (CreationGuideCatalog::CONVERSION_ENTITIES as $entity) {
            $this->assertArrayHasKey($entity, $guides);
            $guide = $guides[$entity];
            $this->assertTrue($guide['ia_for_conversion'] ?? false, $entity);
            $ids = array_column($guide['sections'], 'id');
            $this->assertSame(
                ['philosophie', 'points', 'limites', 'conseils', 'exemples'],
                $ids,
                $entity
            );
        }
    }

    public function test_prompt_strips_krefs_and_keeps_examples(): void
    {
        $catalog = new CreationGuideCatalog;
        $prompt = $catalog->promptFor('spell');

        $this->assertNotNull($prompt);
        $this->assertStringContainsString('# Sorts', $prompt);
        $this->assertStringContainsString('## Philosophie', $prompt);
        $this->assertStringContainsString('Pression', $prompt);
        $this->assertStringNotContainsString('[[kref:', $prompt);
        $this->assertStringContainsString('PA', $prompt);
    }

    public function test_prompt_bundle_covers_all_conversion_entities(): void
    {
        $bundle = (new CreationGuideCatalog)->promptBundle();

        $this->assertSame(CreationGuideCatalog::CONVERSION_ENTITIES, array_keys($bundle));
        $this->assertStringContainsString('Piou Vert', $bundle['monster']);
        $this->assertStringContainsString('Pain d’Incarnam', $bundle['consumable']);
        $this->assertStringContainsString('Fureur', $bundle['capability']);
        $this->assertStringContainsString('Petite taille', $bundle['trait']);
        $this->assertStringContainsString('Blé', $bundle['resource']);
        $this->assertStringContainsString('Cape du Piou Vert', $bundle['item']);
    }
}
