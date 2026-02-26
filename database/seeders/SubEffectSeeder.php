<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SubEffect;
use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Database\Seeder;

/**
 * Seed du référentiel de sous-effets (actions fondamentales).
 * Pattern : action → caractéristique (élément = caractéristique) → valeur.
 * param_schema décrit les paramètres ; "categories" sur characteristic filtre la liste (ex. frapper ⇒ element).
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ARCHITECTURE_EFFETS_3_COUCHES.md
 * @see docs/50-Fonctionnalités/Spell-Effects/NOTATION_SOUS_EFFETS.md
 */
class SubEffectSeeder extends Seeder
{
    public function run(): void
    {
        $sanitizer = new EffectTextSanitizer();

        $rows = [
            [
                'slug' => 'booster',
                'type_slug' => 'booster',
                'template_text' => 'Ajout [characteristic] de [value].',
                'variables_allowed' => ['characteristic', 'value'],
                'param_schema' => [
                    'action' => 'booster',
                    'params' => [
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Caractéristique', 'categories' => ['stat', 'resource']],
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
                        ['key' => 'characteristic', 'type' => 'characteristic', 'label' => 'Caractéristique', 'categories' => ['stat', 'resource']],
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur (formule)'],
                    ],
                ],
            ],
            [
                'slug' => 'soigner',
                'type_slug' => 'soigner',
                'template_text' => 'Soin [value].',
                'variables_allowed' => ['value'],
                'param_schema' => [
                    'action' => 'soigner',
                    'params' => [
                        ['key' => 'value', 'type' => 'formula', 'label' => 'Valeur (formule)'],
                    ],
                ],
            ],
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
        ];

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

        if ($this->command) {
            $this->command->info('SubEffectSeeder : ' . count($rows) . ' sous-effets créés ou mis à jour.');
        }
    }
}
