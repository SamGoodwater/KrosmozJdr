<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute la colonne conversion_function aux tables de groupe caractéristiques.
 * Permet de choisir une fonction PHP enregistrée (en plus ou à la place de conversion_formula)
 * pour des conversions avancées avec accès aux données converties et brutes.
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md
 */
return new class extends Migration
{
    public function up(): void
    {
        $tables = ['characteristic_creature', 'characteristic_object', 'characteristic_spell'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('conversion_function', 64)->nullable()->after('conversion_formula')
                    ->comment('Identifiant d\'une fonction de conversion enregistrée (optionnel)');
            });
        }
    }

    public function down(): void
    {
        $tables = ['characteristic_creature', 'characteristic_object', 'characteristic_spell'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('conversion_function');
            });
        }
    }
};
