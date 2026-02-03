<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_scenario', function (Blueprint $table) {
            $table->foreignId('file_id')->constrained('files')->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->primary(['file_id', 'scenario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_scenario');
    }
};
