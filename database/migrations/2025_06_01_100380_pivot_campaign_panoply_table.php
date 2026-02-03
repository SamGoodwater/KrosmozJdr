<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_panoply', function (Blueprint $table) {
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('panoply_id')->constrained('panoplies')->cascadeOnDelete();
            $table->primary(['campaign_id', 'panoply_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_panoply');
    }
};
