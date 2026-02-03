<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npc_panoply', function (Blueprint $table) {
            $table->foreignId('npc_id')->constrained('npcs')->cascadeOnDelete();
            $table->foreignId('panoply_id')->constrained('panoplies')->cascadeOnDelete();
            $table->primary(['npc_id', 'panoply_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npc_panoply');
    }
};
