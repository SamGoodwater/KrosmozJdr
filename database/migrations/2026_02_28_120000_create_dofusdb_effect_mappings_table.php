<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table de mapping effectId DofusDB → sous-effet KrosmozJDR (action + source de caractéristique).
     *
     * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
     */
    public function up(): void
    {
        Schema::create('dofusdb_effect_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('dofusdb_effect_id')->unique();
            $table->string('sub_effect_slug', 64);
            $table->string('characteristic_source', 32);
            $table->string('characteristic_key', 64)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dofusdb_effect_mappings');
    }
};
