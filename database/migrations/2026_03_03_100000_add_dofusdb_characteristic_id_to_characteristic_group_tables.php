<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1.1 — Ajout de dofusdb_characteristic_id sur les tables de groupe caractéristique.
 * Permet la résolution id DofusDB (API /characteristics) → caractéristique Krosmoz (M2).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_SCRAPPING.md
 * @see docs/50-Fonctionnalités/Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characteristic_creature', function (Blueprint $table) {
            $table->unsignedInteger('dofusdb_characteristic_id')->nullable()->after('characteristic_id')
                ->comment('Id renvoyé par GET /characteristics (DofusDB)');
        });

        Schema::table('characteristic_object', function (Blueprint $table) {
            $table->unsignedInteger('dofusdb_characteristic_id')->nullable()->after('characteristic_id')
                ->comment('Id renvoyé par GET /characteristics (DofusDB), ex. item.effects[].characteristic');
        });

        Schema::table('characteristic_spell', function (Blueprint $table) {
            $table->unsignedInteger('dofusdb_characteristic_id')->nullable()->after('characteristic_id')
                ->comment('Id renvoyé par GET /characteristics (DofusDB)');
        });
    }

    public function down(): void
    {
        Schema::table('characteristic_creature', function (Blueprint $table) {
            $table->dropColumn('dofusdb_characteristic_id');
        });
        Schema::table('characteristic_object', function (Blueprint $table) {
            $table->dropColumn('dofusdb_characteristic_id');
        });
        Schema::table('characteristic_spell', function (Blueprint $table) {
            $table->dropColumn('dofusdb_characteristic_id');
        });
    }
};
