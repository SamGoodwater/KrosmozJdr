<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lien polymorphique entité (spell, item, consumable…) → effect.
     * level_min / level_max définissent la tranche de niveau pour cet effet.
     *
     * @see docs/50-Fonctionnalités/Spell-Effects/MODELE_EFFECT_SOUS_EFFECT.md
     */
    public function up(): void
    {
        Schema::create('effect_usages', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->foreignId('effect_id')->constrained('effects')->cascadeOnDelete();
            $table->unsignedSmallInteger('level_min')->nullable();
            $table->unsignedSmallInteger('level_max')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_usages');
    }
};
