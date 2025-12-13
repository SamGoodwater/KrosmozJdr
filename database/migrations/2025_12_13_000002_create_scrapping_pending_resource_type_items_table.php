<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scrapping_pending_resource_type_items', function (Blueprint $table) {
            $table->id();

            // TypeId DofusDB détecté (non autorisé au moment de la détection)
            $table->unsignedInteger('dofusdb_type_id');

            // ItemId DofusDB à (re)traiter si le type est autorisé plus tard
            $table->unsignedInteger('dofusdb_item_id');

            // Contexte de détection (pour debug/UX)
            $table->string('context')->default('unknown'); // recipe|drops|unknown
            $table->string('source_entity_type')->nullable(); // item|monster|etc
            $table->unsignedInteger('source_entity_dofusdb_id')->nullable();
            $table->unsignedInteger('quantity')->nullable();

            $table->timestamps();

            $table->unique([
                'dofusdb_type_id',
                'dofusdb_item_id',
                'context',
                'source_entity_type',
                'source_entity_dofusdb_id',
            ], 'pending_resource_type_items_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrapping_pending_resource_type_items');
    }
};


