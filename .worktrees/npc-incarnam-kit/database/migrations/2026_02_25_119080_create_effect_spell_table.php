<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sort ↔ définition d’effet (un sort peut réutiliser une définition déjà liée à un autre sort).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('effect_spell', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->foreignId('effect_id')->constrained('effects')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['spell_id', 'effect_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_spell');
    }
};
