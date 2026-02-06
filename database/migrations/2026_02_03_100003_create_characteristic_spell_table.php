<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Caractéristiques du groupe sort : spell.
 * Référence characteristics.id ; contient limites, formules et conversion_formula (Dofus → Krosmoz).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characteristic_spell', function (Blueprint $table) {
            $table->id();
            $table->foreignId('characteristic_id')->constrained('characteristics')->cascadeOnDelete();
            $table->string('entity', 32)->default('spell')->comment('spell ou * = toutes les entités du groupe');
            $table->string('db_column', 64)->nullable();
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->text('formula')->nullable();
            $table->text('formula_display')->nullable();
            $table->string('default_value', 512)->nullable();
            $table->text('conversion_formula')->nullable()->comment('Formule Dofus → Krosmoz');
            $table->json('value_available')->nullable();
            $table->timestamps();

            $table->unique(['characteristic_id', 'entity'], 'char_spell_char_entity_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characteristic_spell');
    }
};
