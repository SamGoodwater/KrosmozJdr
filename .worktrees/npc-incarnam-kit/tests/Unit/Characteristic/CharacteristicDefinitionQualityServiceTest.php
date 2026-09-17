<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Services\Characteristics\CharacteristicDefinitionQualityService;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie la résolution des emplacements DofusDB depuis les aides éditoriales.
 */
class CharacteristicDefinitionQualityServiceTest extends TestCase
{
    public function test_it_resolves_actual_dofusdb_type_ids_for_armor_slots(): void
    {
        $service = new CharacteristicDefinitionQualityService;

        $this->assertSame([16, 17], $service->suggestedDofusTypeIdsForHelper(
            'Équipement : bonus sur chapeau ou cape.'
        ));
        $this->assertSame([11, 17], $service->suggestedDofusTypeIdsForHelper(
            'Équipement (capes, bottes) : bonus.'
        ));
        $this->assertSame([82], $service->suggestedDofusTypeIdsForHelper(
            'Équipement (boucliers) : résistance.'
        ));
    }

    public function test_it_resolves_all_supported_weapon_type_ids(): void
    {
        $service = new CharacteristicDefinitionQualityService;

        $this->assertSame(
            [2, 3, 4, 5, 6, 7, 8, 19, 21, 22, 114, 271],
            $service->suggestedDofusTypeIdsForHelper('Équipement (armes) : dégâts fixes.')
        );
        $this->assertSame(
            [2, 3, 4, 5, 6, 7, 8, 19, 21, 22, 114, 271],
            $service->suggestedDofusTypeIdsForHelper('Équipement (arc) : bonus.')
        );
    }

    public function test_it_does_not_treat_arcanes_as_weapon_arc(): void
    {
        $service = new CharacteristicDefinitionQualityService;

        $this->assertSame(
            [16, 17],
            $service->suggestedDofusTypeIdsForHelper(
                'Équipement : bonus à la compétence Arcanes sur chapeau ou cape (2.2.2).'
            )
        );
    }

    public function test_object_definitions_use_existing_dofusdb_type_ids_and_match_helpers(): void
    {
        $root = dirname(__DIR__, 3);
        /** @var list<array{dofusdb_type_id:int}> $itemTypes */
        $itemTypes = require $root.'/database/seeders/data/item_types.php';
        $existingIds = array_column($itemTypes, 'dofusdb_type_id');
        $service = new CharacteristicDefinitionQualityService;

        foreach (glob($root.'/database/seeders/data/characteristic-definitions/object/*.json') ?: [] as $path) {
            /** @var array<string, mixed> $definition */
            $definition = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $helper = (string) ($definition['characteristic']['helper'] ?? '');
            $expectedIds = $service->suggestedDofusTypeIdsForHelper($helper);
            if ($expectedIds === []) {
                continue;
            }

            $actualIds = $definition['entities']['*']['item_type_dofus_ids'] ?? [];
            $this->assertSame($expectedIds, $actualIds, basename($path));
            foreach ($actualIds as $id) {
                $this->assertContains($id, $existingIds, basename($path));
            }
        }
    }
}
