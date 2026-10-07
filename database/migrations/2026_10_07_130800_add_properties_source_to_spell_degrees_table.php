<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Source du bloc de propriétés d’un degré : propres, degré précédent, ou sort de base.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spell_degrees', function (Blueprint $table) {
            $table->string('properties_source', 16)
                ->default('own')
                ->after('inherits_effects')
                ->comment('own | previous | spell');
        });
    }

    public function down(): void
    {
        Schema::table('spell_degrees', function (Blueprint $table) {
            $table->dropColumn('properties_source');
        });
    }
};
