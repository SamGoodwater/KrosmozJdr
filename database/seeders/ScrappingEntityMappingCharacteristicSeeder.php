<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Characteristic;
use App\Models\Scrapping\ScrappingEntityMapping;
use Illuminate\Database\Seeder;

/**
 * Lie les caractéristiques du groupe object aux règles « bonus » item et panoply
 * (table pivot scrapping_entity_mapping_characteristic).
 *
 * Exclut : CA, recharge wakfu, bonus de sauvegarde, compétences, bonus de touche.
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md
 */
class ScrappingEntityMappingCharacteristicSeeder extends Seeder
{
    /** Clés de caractéristiques object à ne pas lier à bonus (pas d'équivalent DofusDB ou non mappées). */
    private const EXCLUDED_OBJECT_KEYS = [
        'ca_object',
        'wakfu_recharge_object',
        'save_vit_sag_object',
        'save_force_int_cha_agi_object',
        'competences_object',
        'competences_passives_object',
        'touch_object',
    ];

    public function run(): void
    {
        $itemBonus = ScrappingEntityMapping::where('source', 'dofusdb')
            ->where('entity', 'item')
            ->where('mapping_key', 'bonus')
            ->first();

        $panoplyBonus = ScrappingEntityMapping::where('source', 'dofusdb')
            ->where('entity', 'panoply')
            ->where('mapping_key', 'bonus')
            ->first();

        if ($itemBonus === null && $panoplyBonus === null) {
            $this->command?->info('Aucune règle bonus (item/panoply) trouvée. Passez.');

            return;
        }

        $characteristics = Characteristic::where('group', 'object')
            ->whereNotIn('key', self::EXCLUDED_OBJECT_KEYS)
            ->pluck('id')
            ->all();

        if ($characteristics === []) {
            $this->command?->info('Aucune caractéristique object à lier.');

            return;
        }

        $count = 0;
        if ($itemBonus !== null) {
            $itemBonus->characteristics()->syncWithoutDetaching($characteristics);
            $count += count($characteristics);
        }
        if ($panoplyBonus !== null) {
            $panoplyBonus->characteristics()->syncWithoutDetaching($characteristics);
            $count += count($characteristics);
        }

        $this->command?->info('Liens bonus (item + panoply) : ' . count($characteristics) . ' caractéristique(s) liée(s).');
    }
}
