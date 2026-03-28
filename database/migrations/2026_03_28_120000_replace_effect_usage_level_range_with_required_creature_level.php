<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Remplace la tranche level_min / level_max par un seuil unique required_creature_level
 * (niveau minimum du porteur pour « débloquer » cet usage / ce degré).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('effect_usages', function (Blueprint $table) {
            $table->unsignedSmallInteger('required_creature_level')->nullable()->after('effect_id');
        });

        // Données existantes : priorité à level_min ; sinon level_max.
        foreach (DB::table('effect_usages')->orderBy('id')->get() as $row) {
            $min = $row->level_min ?? null;
            $max = $row->level_max ?? null;
            $single = null;
            if ($min !== null) {
                $single = (int) $min;
            } elseif ($max !== null) {
                $single = (int) $max;
            }
            DB::table('effect_usages')->where('id', $row->id)->update([
                'required_creature_level' => $single,
            ]);
        }

        Schema::table('effect_usages', function (Blueprint $table) {
            $table->dropColumn(['level_min', 'level_max']);
        });
    }

    public function down(): void
    {
        Schema::table('effect_usages', function (Blueprint $table) {
            $table->unsignedSmallInteger('level_min')->nullable()->after('effect_id');
            $table->unsignedSmallInteger('level_max')->nullable()->after('level_min');
        });

        foreach (DB::table('effect_usages')->orderBy('id')->get() as $row) {
            $req = $row->required_creature_level ?? null;
            DB::table('effect_usages')->where('id', $row->id)->update([
                'level_min' => $req,
                'level_max' => $req,
            ]);
        }

        Schema::table('effect_usages', function (Blueprint $table) {
            $table->dropColumn('required_creature_level');
        });
    }
};
