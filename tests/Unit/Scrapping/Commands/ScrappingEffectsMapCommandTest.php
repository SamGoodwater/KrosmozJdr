<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Commands;

use App\Console\Commands\Scrapping\Effects\ScrappingEffectsMapCommand;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Vérifie la distinction DofusDB entre bonus, malus et vol de caractéristiques.
 */
class ScrappingEffectsMapCommandTest extends TestCase
{
    public function test_it_classifies_characteristic_directions_before_the_generic_boost_flag(): void
    {
        $effects = [
            [
                'id' => 210,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 33,
                'description_fr' => '#1 à #2% Résistance Terre',
                'boost' => true,
            ],
            [
                'id' => 215,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 33,
                'description_fr' => '-#1 à -#2% Résistance Terre',
                'boost' => true,
            ],
            [
                'id' => 77,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 23,
                'description_fr' => 'Vole #1 à #2 PM',
                'boost' => true,
            ],
            [
                'id' => 1040,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 0,
                'description_fr' => '#1 à #2 Bouclier',
                'boost' => true,
            ],
            [
                'id' => 1039,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 0,
                'description_fr' => 'Bouclier : #1 à #2% des PV max',
                'boost' => true,
            ],
            [
                'id' => 1103,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 0,
                'description_fr' => 'Repousse de #1 case (sans dommages)',
                'boost' => false,
            ],
            [
                'id' => 149,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 38,
                'description_fr' => "Change l'apparence",
                'boost' => true,
            ],
            [
                'id' => 950,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 71,
                'description_fr' => 'État #3',
                'boost' => true,
            ],
            [
                'id' => 1054,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 98,
                'description_fr' => '#1 à #2 Puissance Sorts',
                'boost' => true,
            ],
            [
                'id' => 9095,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 95,
                'description_fr' => '#1 à #2 maxLifePoints',
                'boost' => true,
            ],
            [
                'id' => 9096,
                'category' => 0,
                'elementId' => -1,
                'characteristic' => 0,
                'description_fr' => 'Gain de #1 points de vie temporaires',
                'boost' => true,
            ],
        ];

        $method = new ReflectionMethod(ScrappingEffectsMapCommand::class, 'buildMappingsFromEffects');
        $mappings = $method->invoke(new ScrappingEffectsMapCommand, $effects);

        $this->assertSame(['booster', 'characteristic', 'res_terre'], $mappings[210]);
        $this->assertSame(['retirer', 'characteristic', 'res_terre'], $mappings[215]);
        $this->assertSame(['voler-caracteristiques', 'characteristic', 'pm'], $mappings[77]);
        $this->assertSame(['protéger', 'none', null], $mappings[1040]);
        $this->assertSame(['protéger', 'none', null], $mappings[1039]);
        $this->assertSame(['déplacer', 'none', null], $mappings[1103]);
        $this->assertSame(['autre', 'none', null], $mappings[149]);
        $this->assertArrayNotHasKey(950, $mappings);
        $this->assertSame(['booster', 'characteristic', 'mastery_bonus'], $mappings[1054]);
        $this->assertSame(['donner-pv-temporaires', 'none', null], $mappings[9095]);
        $this->assertSame(['donner-pv-temporaires', 'none', null], $mappings[9096]);
    }
}
