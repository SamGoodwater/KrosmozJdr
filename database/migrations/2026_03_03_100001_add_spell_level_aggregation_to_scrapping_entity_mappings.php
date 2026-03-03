<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1.2 — Ajout de spell_level_aggregation sur scrapping_entity_mappings (M6).
 * Indique comment agréger les valeurs quand plusieurs spell levels (ex. first, max, min, last).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_SCRAPPING.md
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scrapping_entity_mappings', function (Blueprint $table) {
            $table->string('spell_level_aggregation', 16)->nullable()->after('formatters')
                ->comment('Agrégation multi spell-level : first, max, min, last (défaut first)');
        });
    }

    public function down(): void
    {
        Schema::table('scrapping_entity_mappings', function (Blueprint $table) {
            $table->dropColumn('spell_level_aggregation');
        });
    }
};
