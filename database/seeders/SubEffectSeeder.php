<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SubEffect;
use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Database\Seeder;

/**
 * Seed du référentiel de sous-effets (actions fondamentales).
 * Liste : frapper, soigner, protéger, voler-vie, booster, retirer, voler-caracteristiques, invoquer, déplacer.
 * param_schema décrit les paramètres ; categories sur characteristic filtre la liste (element / toutes caractéristiques / monster / sans option).
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ARCHITECTURE_EFFETS_3_COUCHES.md
 */
class SubEffectSeeder extends Seeder
{
    public function run(): void
    {
        $sanitizer = new EffectTextSanitizer();

        $rows = [
            [
                'slug' => 'frapper',
                'type_slug' => 'frapper',
                'template_text' => 'Dégâts [value] [characteristic].',
                'variables_allowed' => ['value', 'characteristic'],
                'param_schema' => [
                    'action' => 'frapper',
                    'params' => [
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Élément', 'categories' => ['element']],
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur (formule)'],
                    ],
                ],
            ],
            [
                'slug' => 'soigner',
                'type_slug' => 'soigner',
                'template_text' => 'Soin [value] [characteristic].',
                'variables_allowed' => ['value', 'characteristic'],
                'param_schema' => [
                    'action' => 'soigner',
                    'params' => [
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Élément', 'categories' => ['element']],
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur (formule)'],
                    ],
                ],
            ],
            [
                'slug' => 'protéger',
                'type_slug' => 'protéger',
                'template_text' => 'Protège la cible.',
                'variables_allowed' => [],
                'param_schema' => ['action' => 'protéger', 'params' => []],
            ],
            [
                'slug' => 'voler-vie',
                'type_slug' => 'voler-vie',
                'template_text' => 'Vol de vie [value] [characteristic].',
                'variables_allowed' => ['value', 'characteristic'],
                'param_schema' => [
                    'action' => 'voler-vie',
                    'params' => [
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Élément', 'categories' => ['element']],
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur (formule)'],
                    ],
                ],
            ],
            [
                'slug' => 'booster',
                'type_slug' => 'booster',
                'template_text' => 'Ajout [characteristic] de [value].',
                'variables_allowed' => ['characteristic', 'value'],
                'param_schema' => [
                    'action' => 'booster',
                    'params' => [
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Caractéristique', 'categories' => ['stat', 'resource', 'element']],
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur (formule)'],
                    ],
                ],
            ],
            [
                'slug' => 'retirer',
                'type_slug' => 'retirer',
                'template_text' => 'Retrait [characteristic] de [value].',
                'variables_allowed' => ['characteristic', 'value'],
                'param_schema' => [
                    'action' => 'retirer',
                    'params' => [
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Caractéristique', 'categories' => ['stat', 'resource', 'element']],
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur (formule)'],
                    ],
                ],
            ],
            [
                'slug' => 'voler-caracteristiques',
                'type_slug' => 'voler-caracteristiques',
                'template_text' => 'Vol [characteristic] de [value].',
                'variables_allowed' => ['characteristic', 'value'],
                'param_schema' => [
                    'action' => 'voler-caracteristiques',
                    'params' => [
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Caractéristique', 'categories' => ['stat', 'resource', 'element']],
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur (formule)'],
                    ],
                ],
            ],
            [
                'slug' => 'invoquer',
                'type_slug' => 'invoquer',
                'template_text' => 'Invocation [monster].',
                'variables_allowed' => ['monster'],
                'param_schema' => [
                    'action' => 'invoquer',
                    'params' => [
                        ['key' => 'monster_id', 'type' => 'monster', 'label' => 'Monstre'],
                    ],
                ],
            ],
            [
                'slug' => 'déplacer',
                'type_slug' => 'déplacer',
                'template_text' => 'Déplace la cible.',
                'variables_allowed' => [],
                'param_schema' => ['action' => 'déplacer', 'params' => []],
            ],
        ];

        $allowedSlugs = array_column($rows, 'slug');

        foreach ($rows as $row) {
            $template_text = isset($row['template_text'])
                ? $sanitizer->sanitize($row['template_text'])
                : null;
            $payload = [
                'type_slug' => $row['type_slug'],
                'template_text' => $template_text,
                'variables_allowed' => $row['variables_allowed'] ?? null,
                'param_schema' => $row['param_schema'] ?? null,
            ];
            SubEffect::updateOrCreate(
                ['slug' => $row['slug']],
                $payload
            );
        }

        // Retirer les anciens sous-effets qui ne font plus partie du référentiel
        SubEffect::whereNotIn('slug', $allowedSlugs)->delete();

        if ($this->command) {
            $this->command->info('SubEffectSeeder : ' . count($rows) . ' sous-effets créés ou mis à jour.');
        }
    }
}
