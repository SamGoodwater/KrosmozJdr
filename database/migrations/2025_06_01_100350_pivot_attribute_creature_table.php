<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_creature', function (Blueprint $table) {
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->foreignId('creature_id')->constrained('creatures')->cascadeOnDelete();
            $table->primary(['attribute_id', 'creature_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_creature');
    }
};
