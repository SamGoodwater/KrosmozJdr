<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Emplacements de sorts par classe : niveau PJ, index d'emplacement, ordre des choix.
 *
 * Rétro-remplissage : chaque sort existant devient un emplacement distinct au niveau 1
 * (slot_index incrémenté par breed), jusqu'à reconfiguration manuelle.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('breed_spell', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->dropForeign(['spell_id']);
        });

        Schema::table('breed_spell', function (Blueprint $table) {
            $table->dropPrimary(['breed_id', 'spell_id']);
        });

        Schema::table('breed_spell', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('character_level')->default(1);
            $table->unsignedTinyInteger('slot_index')->default(1);
            $table->unsignedTinyInteger('choice_order')->default(0);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('
                UPDATE breed_spell bs
                INNER JOIN (
                    SELECT id,
                           ROW_NUMBER() OVER (PARTITION BY breed_id ORDER BY spell_id) AS rn
                    FROM breed_spell
                ) x ON bs.id = x.id
                SET bs.character_level = 1,
                    bs.slot_index = x.rn,
                    bs.choice_order = 0
            ');
        } else {
            $rows = DB::table('breed_spell')->orderBy('breed_id')->orderBy('spell_id')->get();
            $byBreed = $rows->groupBy('breed_id');
            foreach ($byBreed as $spells) {
                $slot = 1;
                foreach ($spells as $row) {
                    DB::table('breed_spell')->where('id', $row->id)->update([
                        'character_level' => 1,
                        'slot_index' => $slot,
                        'choice_order' => 0,
                    ]);
                    $slot++;
                }
            }
        }

        Schema::table('breed_spell', function (Blueprint $table) {
            $table->unique(['breed_id', 'character_level', 'slot_index', 'spell_id'], 'breed_spell_slot_spell_unique');
            $table->index(['breed_id', 'character_level', 'slot_index'], 'breed_spell_level_slot_idx');
            $table->foreign('breed_id')->references('id')->on('breeds')->cascadeOnDelete();
            $table->foreign('spell_id')->references('id')->on('spells')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('breed_spell', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->dropForeign(['spell_id']);
        });

        Schema::table('breed_spell', function (Blueprint $table) {
            $table->dropUnique('breed_spell_slot_spell_unique');
            $table->dropIndex('breed_spell_level_slot_idx');
        });

        Schema::table('breed_spell', function (Blueprint $table) {
            $table->dropColumn(['id', 'character_level', 'slot_index', 'choice_order']);
        });

        Schema::table('breed_spell', function (Blueprint $table) {
            $table->primary(['breed_id', 'spell_id']);
        });

        Schema::table('breed_spell', function (Blueprint $table) {
            $table->foreign('breed_id')->references('id')->on('breeds')->cascadeOnDelete();
            $table->foreign('spell_id')->references('id')->on('spells')->cascadeOnDelete();
        });
    }
};
