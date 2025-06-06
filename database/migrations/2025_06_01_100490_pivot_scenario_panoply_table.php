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
        Schema::create('scenario_panoply', function (Blueprint $table) {
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->foreignId('panoply_id')->constrained('panoplies')->cascadeOnDelete();
            $table->primary(['scenario_id', 'panoply_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_panoply');
    }
};
