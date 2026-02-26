<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SubEffect;
use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Database\Seeder;

/**
 * Seed du référentiel de sous-effets (taper, soigner, vol_pa…).
 * Optionnel en phase 1 ; à étendre ou remplacer par un fichier data en phase 6.
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/MODELE_EFFECT_SOUS_EFFECT.md
 */
class SubEffectSeeder extends Seeder
{
    public function run(): void
    {
        $sanitizer = new EffectTextSanitizer();

        $rows = [
            ['slug' => 'taper', 'type_slug' => 'taper', 'template_text' => 'Inflige [value] dégâts [element].', 'variables_allowed' => ['value', 'element']],
            ['slug' => 'taper-dice', 'type_slug' => 'taper', 'template_text' => 'Inflige ndX dégâts [element].', 'variables_allowed' => ['element']],
            ['slug' => 'soigner', 'type_slug' => 'soigner', 'template_text' => 'Soigne [value] PV.', 'variables_allowed' => ['value']],
            ['slug' => 'vol-pa', 'type_slug' => 'vol_pa', 'template_text' => 'Vol [value] PA.', 'variables_allowed' => ['value']],
            ['slug' => 'vol-pm', 'type_slug' => 'vol_pm', 'template_text' => 'Vol [value] PM.', 'variables_allowed' => ['value']],
            ['slug' => 'buff-agi', 'type_slug' => 'buff', 'template_text' => '+[value] Agilité.', 'variables_allowed' => ['value']],
        ];

        foreach ($rows as $row) {
            $template_text = isset($row['template_text']) ? $sanitizer->sanitize($row['template_text']) : null;
            SubEffect::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'type_slug' => $row['type_slug'],
                    'template_text' => $template_text,
                    'variables_allowed' => $row['variables_allowed'] ?? null,
                ]
            );
        }

        if ($this->command) {
            $this->command->info('SubEffectSeeder : ' . count($rows) . ' sous-effets créés ou mis à jour.');
        }
    }
}
