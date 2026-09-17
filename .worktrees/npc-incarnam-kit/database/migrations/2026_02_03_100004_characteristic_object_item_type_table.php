<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pivot : réserve une définition de caractéristique (groupe object) à certains types d'équipement.
 * Une ligne = (characteristic_object_id, item_type_id). Si une ligne characteristic_object n'a
 * aucune entrée ici, la caractéristique s'applique à tous les types d'items ; sinon uniquement
 * aux item_type_id listés (table item_types).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characteristic_object_item_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('characteristic_object_id')->constrained('characteristic_object')->cascadeOnDelete();
            $table->foreignId('item_type_id')->constrained('item_types')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['characteristic_object_id', 'item_type_id'], 'char_object_item_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characteristic_object_item_type');
    }
};
