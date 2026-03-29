<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_panoply', function (Blueprint $table) {
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('panoply_id')->constrained('panoplies')->cascadeOnDelete();
            $table->primary(['item_id', 'panoply_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_panoply');
    }
};
