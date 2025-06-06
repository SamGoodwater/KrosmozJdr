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
        Schema::create('spell_type', function (Blueprint $table) {
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->foreignId('spell_type_id')->constrained('spell_types')->cascadeOnDelete();
            $table->primary(['spell_id', 'spell_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spell_type');
    }
};
