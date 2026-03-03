<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DofusdbEffectMapping;
use Illuminate\Database\Seeder;

/**
 * Seed des mappings effectId DofusDB → sous-effet Krosmoz (source de vérité en dur).
 *
 * Charge les mappings soit depuis le fichier généré par l’API (si présent), soit depuis
 * la constante MAPPINGS ci-dessous. Pour régénérer le fichier depuis DofusDB :
 *
 *   php artisan scrapping:effects:map --output=database/seeders/data/dofusdb_effect_mappings_suggested.php
 *
 * Puis exécuter ce seeder pour écrire en base. Tu peux aussi éditer MAPPINGS ou le fichier
 * data pour ajouter/corriger des lignes à la main.
 *
 * Référence effectId : API DofusDB GET /effects/{id}.
 * Sous-effets : sub_effects (frapper, soigner, booster, retirer, déplacer, invoquer, autre, …).
 * characteristic_source : element | characteristic | none.
 * characteristic_key : null pour element/none ; clé Krosmoz si characteristic.
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 * @see docs/50-Fonctionnalités/Scrapping/CARACTERISTIQUES_EFFETS_PAR_ACTION.md
 */
class DofusdbEffectMappingSeeder extends Seeder
{
    /** Fichier de mappings suggérés généré par scrapping:effects:map (optionnel). */
    private const DATA_FILE = __DIR__ . '/data/dofusdb_effect_mappings_suggested.php';

    /**
     * Mappings par défaut (utilisés si le fichier data n’existe pas).
     * effectId => [sub_effect_slug, characteristic_source, characteristic_key].
     */
    private const MAPPINGS = [
        96 => ['frapper', 'element', null],
        97 => ['frapper', 'element', null],
        98 => ['frapper', 'element', null],
        99 => ['frapper', 'element', null],
        100 => ['frapper', 'element', null],
    ];

    public function run(): void
    {
        $mappings = $this->getMappings();

        foreach ($mappings as $dofusdbEffectId => $mapping) {
            [$subEffectSlug, $characteristicSource, $characteristicKey] = $mapping;

            DofusdbEffectMapping::updateOrCreate(
                ['dofusdb_effect_id' => $dofusdbEffectId],
                [
                    'sub_effect_slug' => $subEffectSlug,
                    'characteristic_source' => $characteristicSource,
                    'characteristic_key' => $characteristicKey,
                ]
            );
        }
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: string|null}>
     */
    private function getMappings(): array
    {
        if (is_file(self::DATA_FILE)) {
            $loaded = require self::DATA_FILE;
            if (is_array($loaded)) {
                return $loaded;
            }
        }

        return self::MAPPINGS;
    }
}
