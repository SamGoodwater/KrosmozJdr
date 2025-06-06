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
        Schema::create('consumable_creature', function (Blueprint $table) {
            $table->foreignId('consumable_id')->constrained('consumables')->cascadeOnDelete();
            $table->foreignId('creature_id')->constrained('creatures')->cascadeOnDelete();
            $table->string('quantity')->default('1');
            $table->primary(['consumable_id', 'creature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_creature');
    }
};
