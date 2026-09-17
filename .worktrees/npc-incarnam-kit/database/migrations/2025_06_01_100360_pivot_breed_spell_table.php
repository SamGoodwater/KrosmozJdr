<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Emplacements de sorts par classe : niveau PJ, index d'emplacement, ordre des choix.
 *
 * Un même couple (breed, spell) peut apparaître plusieurs fois sur des slots distincts ;
 * l'unicité porte sur (breed_id, character_level, slot_index, spell_id).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breed_spell', function (Blueprint $table) {
            $table->id();
            $table->foreignId('breed_id')->constrained('breeds')->cascadeOnDelete();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->unsignedSmallInteger('character_level')->default(1);
            $table->unsignedTinyInteger('slot_index')->default(1);
            $table->unsignedTinyInteger('choice_order')->default(0);

            $table->unique(['breed_id', 'character_level', 'slot_index', 'spell_id'], 'breed_spell_slot_spell_unique');
            $table->index(['breed_id', 'character_level', 'slot_index'], 'breed_spell_level_slot_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breed_spell');
    }
};
