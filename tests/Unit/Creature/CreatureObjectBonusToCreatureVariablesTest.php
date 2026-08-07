<?php

declare(strict_types=1);

namespace Tests\Unit\Creature;

use App\Contracts\Characteristic\CharacteristicDefinitionLookup;
use App\Services\Creature\Runtime\CreatureObjectBonusToCreatureVariables;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreatureObjectBonusToCreatureVariables::class)]
final class CreatureObjectBonusToCreatureVariablesTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_map_keeps_object_totals_separate_from_base(): void
    {
        $getter = Mockery::mock(CharacteristicDefinitionLookup::class);
        $getter->shouldReceive('getDefinition')->andReturnUsing(static function (string $key, string $entity): ?array {
            return match ($key) {
                'action_points_creature' => ['db_column' => 'pa'],
                default => null,
            };
        });

        $merger = new CreatureObjectBonusToCreatureVariables($getter);
        $mapped = $merger->mapToCharacteristicKeys('monster', [
            'action_points' => 2,
            'acrobatics' => 3,
        ]);

        $this->assertSame(2, $mapped['action_points_creature']);
        $this->assertSame(3, $mapped['acrobatie_bonus']);

        $variables = ['action_points_creature' => 4.0];
        $merger->mergeInto($variables, 'monster', [
            'action_points' => 2,
            'acrobatics' => 3,
        ]);

        $this->assertSame(4.0, $variables['action_points_creature']);
        $this->assertSame(2.0, $variables['action_points_creature_object']);
        $this->assertSame(3.0, $variables['acrobatie_bonus_object']);
    }

    public function test_french_skill_bonus_names_list_is_non_empty(): void
    {
        $names = CreatureObjectBonusToCreatureVariables::frenchSkillBonusVariableNames();
        $this->assertNotEmpty($names);
        $this->assertContains('acrobatie_bonus', $names);
    }
}
