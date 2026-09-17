<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrapping_pending_resource_type_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('dofusdb_type_id');
            $table->unsignedInteger('dofusdb_item_id');
            $table->string('context')->default('unknown');
            $table->string('source_entity_type')->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('scrapping_pending_resource_type_items');
    }
};
