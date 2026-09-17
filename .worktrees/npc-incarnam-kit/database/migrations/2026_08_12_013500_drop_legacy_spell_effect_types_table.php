<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Supprime le référentiel legacy `spell_effect_types` (remplacé par SubEffect / Effect).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('spell_effects');
        Schema::dropIfExists('spell_effect_types');
    }

    public function down(): void
    {
        if (! Schema::hasTable('spell_effect_types')) {
            Schema::create('spell_effect_types', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('category', 32)->nullable();
                $table->string('unit', 16)->nullable();
                $table->string('value_type', 16)->nullable();
                $table->string('element', 16)->nullable();
                $table->boolean('is_positive')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->unsignedInteger('dofusdb_effect_id')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }
};
