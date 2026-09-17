<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Conversion\SpellEffects;

use App\Models\Entity\Spell;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellResolutionInferenceService;
use PHPUnit\Framework\TestCase;

/**
 * Vérifie l’inférence résolution / Wakfu selon les règles 3.3.2.3.
 */
final class SpellResolutionInferenceServiceTest extends TestCase
{
    private SpellResolutionInferenceService $service;

    protected function setUp(): void
    {
        $this->service = new SpellResolutionInferenceService;
    }

    public function test_pure_single_target_damage_uses_physical_attack_roll(): void
    {
        $resolution = $this->service->infer([
            [
                'area' => 'point',
                'sub_effects' => [
                    [
                        'sub_effect_slug' => 'frapper',
                        'params' => ['characteristic' => 'fire', 'value_converted' => 8],
                    ],
                ],
            ],
        ]);

        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $resolution['resolution_mode']);
        $this->assertSame('intel', $resolution['attack_characteristic_key']);
        $this->assertFalse($resolution['is_magic']);
        $this->assertNull($resolution['save_characteristic_key']);
    }

    public function test_area_damage_uses_saving_throw_and_is_magic(): void
    {
        $resolution = $this->service->infer([
            [
                'area' => 'circle-2',
                'sub_effects' => [
                    [
                        'sub_effect_slug' => 'frapper',
                        'params' => ['characteristic' => 'water', 'value_converted' => 10],
                    ],
                ],
            ],
        ]);

        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $resolution['resolution_mode']);
        $this->assertTrue($resolution['is_magic']);
        $this->assertSame('sagesse', $resolution['save_characteristic_key']);
        $this->assertSame(
            SpellResolutionInferenceService::SAVE_DC_DEFAULT_FORMULA,
            $resolution['save_dc_formula']
        );
    }

    public function test_hostile_state_uses_saving_throw(): void
    {
        $resolution = $this->service->infer([
            [
                'area' => 'point',
                'sub_effects' => [
                    [
                        'sub_effect_slug' => 'appliquer-etat',
                        'params' => [
                            'condition_name' => 'Pesanteur',
                            'condition_flags' => ['cant_be_moved' => true],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $resolution['resolution_mode']);
        $this->assertSame('strong', $resolution['save_characteristic_key']);
        $this->assertTrue($resolution['is_magic']);
    }

    public function test_single_target_pa_removal_uses_attack_roll(): void
    {
        $resolution = $this->service->infer([
            [
                'area' => 'point',
                'sub_effects' => [
                    [
                        'sub_effect_slug' => 'retirer',
                        'params' => ['characteristic' => 'pa', 'value_converted' => 2],
                    ],
                ],
            ],
        ]);

        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $resolution['resolution_mode']);
        $this->assertFalse($resolution['is_magic']);
    }

    public function test_damage_plus_pa_removal_stays_attack_roll(): void
    {
        $resolution = $this->service->infer([
            [
                'area' => 'point',
                'sub_effects' => [
                    [
                        'sub_effect_slug' => 'frapper',
                        'params' => ['characteristic' => 'air', 'value_converted' => 8],
                    ],
                    [
                        'sub_effect_slug' => 'retirer',
                        'params' => ['characteristic' => 'pm', 'value_converted' => 1],
                    ],
                ],
            ],
        ]);

        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $resolution['resolution_mode']);
        $this->assertSame('agi', $resolution['attack_characteristic_key']);
        $this->assertFalse($resolution['is_magic']);
    }

    public function test_area_pa_removal_uses_saving_throw(): void
    {
        $resolution = $this->service->infer([
            [
                'area' => 'circle-2',
                'sub_effects' => [
                    [
                        'sub_effect_slug' => 'retirer',
                        'params' => ['characteristic' => 'pa', 'value_converted' => 1],
                    ],
                ],
            ],
        ]);

        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $resolution['resolution_mode']);
        $this->assertTrue($resolution['is_magic']);
        $this->assertSame('sagesse', $resolution['save_characteristic_key']);
    }

    public function test_push_without_damage_uses_strength_save(): void
    {
        $resolution = $this->service->infer([
            [
                'area' => 'point',
                'sub_effects' => [
                    [
                        'sub_effect_slug' => 'déplacer',
                        'params' => ['movement_kind' => 'push', 'value_converted' => 3],
                    ],
                ],
            ],
        ]);

        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $resolution['resolution_mode']);
        $this->assertSame('strong', $resolution['save_characteristic_key']);
    }

    public function test_support_only_is_auto_success_and_magic(): void
    {
        $resolution = $this->service->infer([
            [
                'area' => 'point',
                'sub_effects' => [
                    [
                        'sub_effect_slug' => 'soigner',
                        'params' => ['value_converted' => 6],
                    ],
                    [
                        'sub_effect_slug' => 's-appliquer-etat',
                        'params' => ['condition_name' => 'Stimulé'],
                    ],
                ],
            ],
        ]);

        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $resolution['resolution_mode']);
        $this->assertTrue($resolution['is_magic']);
        $this->assertNull($resolution['attack_characteristic_key']);
    }
}
