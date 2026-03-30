<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Supprime le champ spells.number_between_two_cast_editable (inutile métier)
 * et nettoie caractéristiques + mapping scrapping associés.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('spells') || ! Schema::hasColumn('spells', 'number_between_two_cast_editable')) {
            return;
        }

        if (Schema::hasTable('scrapping_entity_mappings')) {
            DB::table('scrapping_entity_mappings')
                ->where('source', 'dofusdb')
                ->where('entity', 'spell')
                ->where('mapping_key', 'number_between_two_cast_editable')
                ->delete();
        }

        $charId = Schema::hasTable('characteristics')
            ? DB::table('characteristics')->where('key', 'number_between_two_cast_editable_spell')->value('id')
            : null;

        if ($charId && Schema::hasTable('characteristic_spell')) {
            DB::table('characteristic_spell')->where('characteristic_id', $charId)->delete();
        }

        if ($charId) {
            DB::table('characteristics')->where('id', $charId)->delete();
        }

        Schema::table('spells', function (Blueprint $table) {
            $table->dropColumn('number_between_two_cast_editable');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('spells') || Schema::hasColumn('spells', 'number_between_two_cast_editable')) {
            return;
        }

        Schema::table('spells', function (Blueprint $table) {
            $table->boolean('number_between_two_cast_editable')->default(true);
        });
    }
};
