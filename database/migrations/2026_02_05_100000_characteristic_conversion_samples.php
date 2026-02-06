<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les champs d’échantillons de conversion (Dofus / Krosmoz par niveau)
 * pour l’affichage de graphiques et l’aide à la création de la formule.
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
                $t->json('conversion_dofus_sample')->nullable()->after('conversion_formula')
                    ->comment('Niveau → valeur Dofus (ex. {"1":200,"200":50000})');
                $t->json('conversion_krosmoz_sample')->nullable()->after('conversion_dofus_sample')
                    ->comment('Niveau → valeur Krosmoz (ex. {"1":1,"20":20})');
            });
        }
    }

    public function down(): void
    {
        $tables = ['characteristic_creature', 'characteristic_object', 'characteristic_spell'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn(['conversion_dofus_sample', 'conversion_krosmoz_sample']);
            });
        }
    }
};
