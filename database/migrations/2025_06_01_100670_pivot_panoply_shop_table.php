<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panoply_shop', function (Blueprint $table) {
            $table->foreignId('panoply_id')->constrained('panoplies')->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->primary(['panoply_id', 'shop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panoply_shop');
    }
};
