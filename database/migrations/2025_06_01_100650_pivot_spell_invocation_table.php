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
        Schema::create('spell_invocation', function (Blueprint $table) {
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->foreignId('monster_id')->constrained('monsters')->cascadeOnDelete();
            $table->primary(['spell_id', 'monster_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spell_invocation');
    }
};
