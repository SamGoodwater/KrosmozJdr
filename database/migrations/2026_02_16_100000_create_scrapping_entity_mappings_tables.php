<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables pour le mapping DofusDB → KrosmozJDR par entité (remplace / complète le JSON).
 * Une règle de mapping = une clé logique (ex. level, name) pour une source+entité,
 * avec chemin DofusDB, formatters et cibles (model.field) multiples possibles.
 *
 * @see docs/50-Fonctionnalités/VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrapping_entity_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('source', 64)->default('dofusdb')->comment('Ex: dofusdb');
            $table->string('entity', 64)->comment('Ex: monster, spell, breed, item');
            $table->string('mapping_key', 128)->comment('Clé logique ex: level, name, dofusdb_id');
            $table->string('from_path', 256)->comment('Chemin dans la réponse API ex: grades.0.level');
            $table->boolean('from_lang_aware')->default(false)->comment('Valeur multilingue {fr, en}');
            $table->unsignedBigInteger('characteristic_id')->nullable()->comment('Si conversion/limites via une caractéristique');
            $table->foreign('characteristic_id', 'scr_ent_mappings_characteristic_id_fk')->references('id')->on('characteristics')->nullOnDelete();
            $table->json('formatters')->nullable()->comment('Liste {name, args} ex: [{"name":"toString","args":{}}]');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['source', 'entity', 'mapping_key'], 'scrapping_entity_mappings_source_entity_key_unique');
        });

        Schema::create('scrapping_entity_mapping_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scrapping_entity_mapping_id');
            $table->foreign('scrapping_entity_mapping_id', 'scr_ent_mapping_targets_mapping_fk')->references('id')->on('scrapping_entity_mappings')->cascadeOnDelete();
            $table->string('target_model', 64)->comment('Ex: creatures, monsters, spells');
            $table->string('target_field', 64)->comment('Ex: level, name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['scrapping_entity_mapping_id', 'target_model', 'target_field'], 'scrapping_mapping_targets_rule_model_field_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrapping_entity_mapping_targets');
        Schema::dropIfExists('scrapping_entity_mappings');
    }
};
