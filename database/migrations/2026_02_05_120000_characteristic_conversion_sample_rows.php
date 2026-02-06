<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute le champ conversion_sample_rows pour stocker les lignes du tableau
 * (niveau Dofus, valeur Dofus, niveau Krosmoz, valeur Krosmoz) et préserver l’ordre des paires.
 */
return new class extends Migration
{
    public function up(): void
    {
        $tables = ['characteristic_creature', 'characteristic_object', 'characteristic_spell'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->json('conversion_sample_rows')->nullable()->after('conversion_krosmoz_sample')
                    ->comment('Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]');
            });
        }
    }

    public function down(): void
    {
        $tables = ['characteristic_creature', 'characteristic_object', 'characteristic_spell'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('conversion_sample_rows');
            });
        }
    }
};
