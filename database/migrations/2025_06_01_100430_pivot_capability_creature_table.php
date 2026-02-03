<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capability_creature', function (Blueprint $table) {
            $table->foreignId('capability_id')->constrained('capabilities')->cascadeOnDelete();
            $table->foreignId('creature_id')->constrained('creatures')->cascadeOnDelete();
            $table->primary(['capability_id', 'creature_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capability_creature');
    }
};
