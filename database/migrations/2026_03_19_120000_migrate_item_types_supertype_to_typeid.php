<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : item_types stockait des superTypeIds (Amulette, Arme, Anneau…).
 * On passe à des typeIds (Arc, Baguette, Épée…).
 *
 * - Nullifie item_type_id sur items/characteristic_object qui pointent vers d’anciens ItemType.
 * - Supprime les ItemType dont dofusdb_type_id est un superTypeId equipment.
 * - L’utilisateur relancera : php artisan db:seed --class=TypeSeeder
 */
return new class extends Migration
{
    /** SuperTypeIds equipment (ancien schéma). */
    private const OLD_SUPERTYPE_IDS = [1, 2, 3, 4, 5, 7, 10, 11, 12, 13, 20, 22, 23, 24, 25, 69];

    public function up(): void
    {
        $idsToDelete = DB::table('item_types')
            ->whereIn('dofusdb_type_id', self::OLD_SUPERTYPE_IDS)
            ->pluck('id')
            ->all();

        if (empty($idsToDelete)) {
            return;
        }

        if (Schema::hasTable('items')) {
            DB::table('items')
                ->whereIn('item_type_id', $idsToDelete)
                ->update(['item_type_id' => null]);
        }

        if (Schema::hasTable('characteristic_object_item_type')) {
            DB::table('characteristic_object_item_type')
                ->whereIn('item_type_id', $idsToDelete)
                ->delete();
        }

        DB::table('item_types')->whereIn('id', $idsToDelete)->delete();
    }

    public function down(): void
    {
        // Irréversible : on ne recrée pas les anciennes lignes superTypeId
    }
};
