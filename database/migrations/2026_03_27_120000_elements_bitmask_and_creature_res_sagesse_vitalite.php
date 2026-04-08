<?php

use App\Support\ElementBitmask;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creatures', function (Blueprint $table) {
            $table->string('res_sagesse')->default('0')->after('res_eau');
            $table->string('res_vitalite')->default('0')->after('res_sagesse');
            $table->string('do_sagesse')->default('0')->after('do_fixe_eau');
            $table->string('do_vitalite')->default('0')->after('do_sagesse');
        });

        foreach (['spells', 'capabilities'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::table($tableName)->orderBy('id')->chunkById(200, function ($rows) use ($tableName) {
                    foreach ($rows as $row) {
                        $el = (int) ($row->element ?? 0);
                        $mask = ElementBitmask::normalize($el);
                        DB::table($tableName)->where('id', $row->id)->update(['element' => $mask]);
                    }
                });
            } else {
                $rows = DB::table($tableName)->select('id', 'element')->get();
                foreach ($rows as $row) {
                    $el = (int) ($row->element ?? 0);
                    $mask = ElementBitmask::normalize($el);
                    DB::table($tableName)->where('id', $row->id)->update(['element' => $mask]);
                }
            }
        }

        if (Schema::hasTable('characteristics') && Schema::hasTable('characteristic_spell')) {
            $charId = DB::table('characteristics')->where('key', 'element_spell')->value('id');
            if ($charId) {
                DB::table('characteristic_spell')->where('characteristic_id', $charId)->update([
                    'max' => '127',
                    'value_available' => null,
                    'formula_display' => 'Élément : masque 7 bits (Neutre, Terre, Feu, Air, Eau, Sagesse, Vitalité ; combinaisons 1–127).',
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('creatures', function (Blueprint $table) {
            $table->dropColumn(['res_sagesse', 'res_vitalite', 'do_sagesse', 'do_vitalite']);
        });

        // Pas de restauration fiable 29→masque : laisser les masques en base.
    }
};
