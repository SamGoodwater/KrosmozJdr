<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Définition d’effet (généralités) : partagée entre degrés ; liée aux sorts via effect_spell.
 * Zone et seuil de niveau portés par {@see effect_degrees}.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug', 64)->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('target_type', 32)->default('direct');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effects');
    }
};
