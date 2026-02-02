<?php

declare(strict_types=1);

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
        Schema::create('characteristic_entities', function (Blueprint $table) {
            $table->id();
            $table->string('characteristic_id', 64);
            $table->string('entity', 16);
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->text('formula')->nullable();
            $table->text('formula_display')->nullable();
            $table->string('default_value', 255)->nullable();
            $table->boolean('required')->default(false);
            $table->text('validation_message')->nullable();
            $table->timestamps();

            $table->foreign('characteristic_id')->references('id')->on('characteristics')->cascadeOnDelete();
            $table->unique(['characteristic_id', 'entity'], 'char_entity_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characteristic_entities');
    }
};
