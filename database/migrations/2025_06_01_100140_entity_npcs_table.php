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
        Schema::create('npcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creature_id')->nullable()->constrained('creatures')->cascadeOnDelete();
            $table->string('story')->nullable();
            $table->string('historical')->nullable();
            $table->string('age')->nullable();
            $table->string('size')->nullable();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->cascadeOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('npcs');
    }
};
