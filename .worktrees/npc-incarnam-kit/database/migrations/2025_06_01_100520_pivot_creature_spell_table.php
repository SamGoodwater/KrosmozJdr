<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creature_spell', function (Blueprint $table) {
            $table->foreignId('creature_id')->constrained('creatures')->cascadeOnDelete();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->primary(['creature_id', 'spell_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_spell');
    }
};
