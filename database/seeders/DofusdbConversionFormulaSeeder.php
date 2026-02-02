<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DofusdbConversionFormula;
use Illuminate\Database\Seeder;

/**
 * Importe config/dofusdb_conversion.formulas vers la table dofusdb_conversion_formulas.
 *
 * Prérequis : exécuter CharacteristicConfigSeeder avant (les characteristic_id doivent exister).
 */
class DofusdbConversionFormulaSeeder extends Seeder
{
    public function run(): void
    {
        $formulas = config('dofusdb_conversion.formulas', []);

        if (empty($formulas)) {
            if ($this->command) {
                $this->command->warn('Aucune formule dans config("dofusdb_conversion.formulas").');
            }

            return;
        }

        // level : k = d / divisor (même formule pour monster, class, item)
        $levelConfig = $formulas['level'] ?? null;
        if (is_array($levelConfig)) {
            $divisor = (int) ($levelConfig['divisor'] ?? 10);
            foreach (['monster', 'class', 'item'] as $entity) {
                DofusdbConversionFormula::updateOrCreate(
                    ['characteristic_id' => 'level', 'entity' => $entity],
                    [
                        'formula_type' => 'linear',
                        'parameters' => ['divisor' => $divisor],
                        'formula_display' => 'k = d / ' . $divisor,
                    ]
                );
            }
        }

        // life : k = d / divisor + level * level_factor (monster, class)
        $lifeConfig = $formulas['life'] ?? null;
        if (is_array($lifeConfig)) {
            $divisor = (int) ($lifeConfig['divisor'] ?? 200);
            $levelFactor = (int) ($lifeConfig['level_factor'] ?? 5);
            foreach (['monster', 'class'] as $entity) {
                DofusdbConversionFormula::updateOrCreate(
                    ['characteristic_id' => 'life', 'entity' => $entity],
                    [
                        'formula_type' => 'linear_with_level',
                        'parameters' => ['divisor' => $divisor, 'level_factor' => $levelFactor],
                        'formula_display' => 'k = d / ' . $divisor . ' + [level] * ' . $levelFactor,
                    ]
                );
            }
        }

        // attributes (strength, intelligence, chance, agility) : sqrt par entity
        $attrConfig = $formulas['attributes'] ?? null;
        $attributeIds = $formulas['attribute_ids'] ?? ['strength', 'intelligence', 'chance', 'agility'];
        if (is_array($attrConfig) && is_array($attributeIds)) {
            $denom = (float) ($attrConfig['denom'] ?? 1150);
            $offset = (float) ($attrConfig['offset'] ?? 50);
            foreach ($attributeIds as $charId) {
                foreach (['monster', 'class', 'item'] as $entity) {
                    $entityParams = $attrConfig[$entity] ?? $attrConfig['monster'] ?? ['base' => 0, 'coeff' => 26];
                    $base = (float) ($entityParams['base'] ?? 0);
                    $coeff = (float) ($entityParams['coeff'] ?? 26);
                    DofusdbConversionFormula::updateOrCreate(
                        ['characteristic_id' => $charId, 'entity' => $entity],
                        [
                            'formula_type' => 'sqrt_attribute',
                            'parameters' => [
                                'base' => $base,
                                'coeff' => $coeff,
                                'offset' => $offset,
                                'denom' => $denom,
                            ],
                            'formula_display' => sprintf('k = %s + %s * sqrt((d - %s) / %s)', $base, $coeff, $offset, $denom),
                        ]
                    );
                }
            }
        }

        // initiative (caractéristique id = 'ini' dans characteristics)
        $iniConfig = $formulas['initiative'] ?? null;
        if (is_array($iniConfig)) {
            foreach (['monster', 'class'] as $entity) {
                $params = $iniConfig[$entity] ?? $iniConfig['monster'] ?? [];
                $offset = (float) ($params['offset'] ?? 500);
                $denom = (float) ($params['denom'] ?? 5000);
                $factor = (float) ($params['factor'] ?? 10);
                $clampRatioMinZero = (bool) ($params['clamp_ratio_min_zero'] ?? false);
                $minZero = (bool) ($params['min_zero'] ?? false);
                DofusdbConversionFormula::updateOrCreate(
                    ['characteristic_id' => 'ini', 'entity' => $entity],
                    [
                        'formula_type' => 'ratio_initiative',
                        'parameters' => [
                            'offset' => $offset,
                            'denom' => $denom,
                            'factor' => $factor,
                            'clamp_ratio_min_zero' => $clampRatioMinZero,
                            'min_zero' => $minZero,
                        ],
                        'formula_display' => sprintf('ratio = (d - %s) / %s ; k = %s * ratio', $offset, $denom, $factor),
                    ]
                );
            }
        }

        // Résistances : handler batch (res_neutre = ancre ; produit res_* et res_fixe_* pour tous les éléments)
        // Prérequis : res_neutre (et res_terre, etc.) doivent exister (CharacteristicConfigSeeder).
        foreach (['monster', 'class', 'item'] as $entity) {
            DofusdbConversionFormula::updateOrCreate(
                ['characteristic_id' => 'res_neutre', 'entity' => $entity],
                [
                    'formula_type' => 'custom',
                    'parameters' => [
                        'max_invulnerable' => 1,
                        'max_resistant' => 3,
                        'max_weak' => 3,
                        'max_vulnerable' => 2,
                    ],
                    'conversion_formula' => null,
                    'formula_display' => 'Résistances Dofus → tiers JDR (50/100/-50/-100) + plafonds',
                    'handler_name' => 'resistance_dofus_to_krosmoz',
                ]
            );
        }

        if ($this->command) {
            $count = DofusdbConversionFormula::count();
            $this->command->info('DofusdbConversionFormulaSeeder : ' . $count . ' formule(s) importée(s).');
        }
    }
}
