<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Renommage de l'entité classe en breed : tables et colonnes.
     * L'affichage utilisateur reste « Classe » ; le code et la BDD utilisent breed/breeds.
     */
    public function up(): void
    {
        // 1. Nouvelle table pivot breed_spell et copie des données
        Schema::create('breed_spell', function (Blueprint $table) {
            $table->foreignId('breed_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->primary(['breed_id', 'spell_id']);
        });
        DB::statement('INSERT INTO breed_spell (breed_id, spell_id) SELECT classe_id, spell_id FROM class_spell');
        Schema::dropIfExists('class_spell');

        // 2. Renommer la table principale
        Schema::rename('classes', 'breeds');

        // 3. Mettre à jour la FK sur breed_spell pour pointer vers breeds
        Schema::table('breed_spell', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
        });
        Schema::table('breed_spell', function (Blueprint $table) {
            $table->foreign('breed_id')->references('id')->on('breeds')->cascadeOnDelete();
        });

        // 4. npcs : renommer colonne classe_id -> breed_id
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeign(['classe_id']);
        });
        Schema::table('npcs', function (Blueprint $table) {
            $table->renameColumn('classe_id', 'breed_id');
        });
        Schema::table('npcs', function (Blueprint $table) {
            $table->foreign('breed_id')->references('id')->on('breeds')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
        });
        Schema::table('npcs', function (Blueprint $table) {
            $table->renameColumn('breed_id', 'classe_id');
        });
        Schema::table('npcs', function (Blueprint $table) {
            $table->foreign('classe_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        Schema::rename('breeds', 'classes');

        Schema::table('breed_spell', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
        });
        Schema::create('class_spell', function (Blueprint $table) {
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->primary(['classe_id', 'spell_id']);
        });
        DB::statement('INSERT INTO class_spell (classe_id, spell_id) SELECT breed_id, spell_id FROM breed_spell');
        Schema::dropIfExists('breed_spell');
    }
};
