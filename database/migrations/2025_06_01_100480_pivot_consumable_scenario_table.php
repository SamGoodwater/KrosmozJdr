<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_scenario', function (Blueprint $table) {
            $table->foreignId('consumable_id')->constrained('consumables')->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->primary(['consumable_id', 'scenario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumable_scenario');
    }
};
