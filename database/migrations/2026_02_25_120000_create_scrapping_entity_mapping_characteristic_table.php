<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table pivot : plusieurs caractéristiques peuvent être liées à une même règle de mapping
 * (ex. bonus item / bonus panoply liés à sagesse, force, pa, etc.).
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrapping_entity_mapping_characteristic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scrapping_entity_mapping_id');
            $table->unsignedBigInteger('characteristic_id');
            $table->timestamps();

            $table->foreign('scrapping_entity_mapping_id', 'scr_ent_map_char_mapping_fk')
                ->references('id')->on('scrapping_entity_mappings')->cascadeOnDelete();
            $table->foreign('characteristic_id', 'scr_ent_map_char_characteristic_fk')
                ->references('id')->on('characteristics')->cascadeOnDelete();
            $table->unique(
                ['scrapping_entity_mapping_id', 'characteristic_id'],
                'scr_ent_map_char_mapping_characteristic_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrapping_entity_mapping_characteristic');
    }
};
