<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SubEffect;
use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Database\Seeder;

/**
 * Seed du référentiel de sous-effets (actions fondamentales).
 * Liste : frapper (dommages + vol de vie optionnel via life_steal_formula), soigner, protéger, donner-pv-temporaires, booster, retirer, voler-caracteristiques, invoquer, déplacer, appliquer-etat, s-appliquer-etat, autre.
 * param_schema décrit les paramètres ; categories sur characteristic filtre la liste (element / toutes caractéristiques / monster / sans option / skill).
 *
 * @see docs/features/effects/README.md
 */
class SubEffectSeeder extends Seeder
{
    public function run(): void
    {
        $sanitizer = new EffectTextSanitizer;

        $rows = [
            [
                'slug' => 'frapper',
                'type_slug' => 'frapper',
                'template_text' => 'Dégâts [value] [characteristic].',
                'variables_allowed' => ['value', 'characteristic', 'dgt'],
                'param_schema' => [
                    'action' => 'frapper',
                    'params' => [
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Élément', 'categories' => ['element']],
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur (formule)'],
                        ['key' => 'life_steal_formula', 'type' => 'formula', 'label' => 'PV volés (formule, optionnel)', 'optional' => true],
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
                'template_text' => 'Protège la cible ([value]).',
                'variables_allowed' => ['value'],
                'param_schema' => [
                    'action' => 'protéger',
                    'params' => [
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Protection (formule)'],
                    ],
                ],
            ],
            [
                'slug' => 'donner-pv-temporaires',
                'type_slug' => 'donner-pv-temporaires',
                'template_text' => 'PV temporaires [value].',
                'variables_allowed' => ['value'],
                'param_schema' => [
                    'action' => 'donner-pv-temporaires',
                    'params' => [
                        ['key' => 'value', 'type' => 'formula', 'label' => 'PV temporaires (formule)'],
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
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Caractéristique', 'categories' => ['stat', 'resource', 'element', 'skill']],
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
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Caractéristique', 'categories' => ['stat', 'resource', 'element', 'skill']],
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
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Caractéristique', 'categories' => ['stat', 'resource', 'element', 'skill']],
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
                'template_text' => 'Déplace la cible de [cells] case(s).',
                'variables_allowed' => ['cells', 'teleport'],
                'param_schema' => [
                    'action' => 'déplacer',
                    'params' => [
                        ['key' => 'cells_formula', 'type' => 'formula', 'label' => 'Nombre de cases (formule, décimales autorisées ; 1 case = 1,5 m)'],
                        ['key' => 'movement_kind', 'type' => 'select', 'label' => 'Type de mouvement', 'optional' => true, 'options' => [
                            ['value' => 'movement', 'label' => 'Déplacement'],
                            ['value' => 'jump', 'label' => 'Saut / bond'],
                            ['value' => 'teleport', 'label' => 'Téléportation'],
                            ['value' => 'push', 'label' => 'Repousse'],
                            ['value' => 'pull', 'label' => 'Attirance'],
                        ]],
                        ['key' => 'teleport', 'type' => 'bool', 'label' => 'Téléportation'],
                    ],
                ],
            ],
            [
                'slug' => 'appliquer-etat',
                'type_slug' => 'appliquer-etat',
                'template_text' => 'Applique l\'état [condition_name].',
                'variables_allowed' => ['condition_name'],
                'param_schema' => [
                    'action' => 'appliquer-etat',
                    'params' => [
                        ['key' => 'condition_id', 'type' => 'condition', 'label' => 'État'],
                        ['key' => 'dispellable', 'type' => 'bool', 'label' => 'Dissipable', 'optional' => true],
                    ],
                ],
            ],
            [
                'slug' => 's-appliquer-etat',
                'type_slug' => 's-appliquer-etat',
                'template_text' => 'S\'applique l\'état [condition_name].',
                'variables_allowed' => ['condition_name'],
                'param_schema' => [
                    'action' => 's-appliquer-etat',
                    'params' => [
                        ['key' => 'condition_id', 'type' => 'condition', 'label' => 'État'],
                        ['key' => 'dispellable', 'type' => 'bool', 'label' => 'Dissipable', 'optional' => true],
                    ],
                ],
            ],
            [
                'slug' => 'autre',
                'type_slug' => 'autre',
                'template_text' => '[value].',
                'variables_allowed' => ['value'],
                'param_schema' => [
                    'action' => 'autre',
                    'params' => [
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur ou description (ex. description DofusDB)'],
                    ],
                ],
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
            $this->command->info('SubEffectSeeder : '.count($rows).' sous-effets créés ou mis à jour.');
        }
    }
}
