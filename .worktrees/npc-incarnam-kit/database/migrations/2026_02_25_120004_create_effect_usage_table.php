<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lien polymorphique entité (item, consumable, resource…) → degré d’effet.
 * Les sorts utilisent {@see effect_spell} + seuil sur effect_degrees.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('effect_usages', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->foreignId('effect_degree_id')->constrained('effect_degrees')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_usages');
    }
};
