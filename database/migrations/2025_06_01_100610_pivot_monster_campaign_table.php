<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monster_campaign', function (Blueprint $table) {
            $table->foreignId('monster_id')->constrained('monsters')->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->primary(['monster_id', 'campaign_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monster_campaign');
    }
};
