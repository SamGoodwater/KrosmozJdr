<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table générale des caractéristiques : propriétés communes et id unique.
 * Une ligne = une caractéristique (ex. PA créature, PA sort, PA objet sont 3 lignes distinctes).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characteristics', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique()->comment('Clé unique ex: pa_creature, level_object, pa_spell');
            $table->string('name');
            $table->string('short_name', 64)->nullable();
            $table->text('helper')->nullable();
            $table->text('descriptions')->nullable();
            $table->string('icon', 64)->nullable();
            $table->string('icon_false', 64)->nullable()->comment('Icône alternative pour valeur booléenne « faux »');
            $table->string('color', 32)->nullable();
            $table->json('value_overrides')->nullable()->comment('Surcharges visuelles par valeur : [{ value, icon?, color?, subtitle? }]');
            $table->string('unit', 32)->nullable();
            $table->string('type', 16)->default('string');
            $table->string('status', 32)->default('a_valider')->comment('État de validation interne : a_valider, en_cours_de_validation, validee');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('group', 16)->nullable()->comment('Groupe principal : creature, object ou spell');
            $table->boolean('hide_when_empty')->default(false)->comment('Masquer la ligne en jeu lorsque la valeur est vide / nulle / zéro');
            $table->boolean('hide_when_false')->default(false)->comment('Masquer la ligne en jeu lorsque la valeur booléenne est fausse');
            $table->foreignId('linked_to_characteristic_id')->nullable()->constrained('characteristics')->nullOnDelete()->comment('Caractéristique maître si cette ligne est une caractéristique liée');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characteristics');
    }
};
