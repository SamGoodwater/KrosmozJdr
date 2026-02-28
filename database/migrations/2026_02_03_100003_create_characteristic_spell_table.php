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
            $table->string('min', 512)->nullable();
            $table->string('max', 512)->nullable();
            $table->text('formula')->nullable();
            $table->text('formula_display')->nullable();
            $table->string('default_value', 512)->nullable();
            $table->text('conversion_formula')->nullable()->comment('Formule Dofus → Krosmoz');
            $table->string('conversion_function', 64)->nullable()->comment('Identifiant d\'une fonction de conversion enregistrée (optionnel)');
            $table->json('conversion_dofus_sample')->nullable()->comment('Niveau → valeur Dofus (ex. {"1":200,"200":50000})');
            $table->json('conversion_krosmoz_sample')->nullable()->comment('Niveau → valeur Krosmoz (ex. {"1":1,"20":20})');
            $table->json('conversion_sample_rows')->nullable()->comment('Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]');
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
