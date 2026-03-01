<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Évite les deadlocks MySQL lors de migrate:fresh (pivots vers campaigns). */
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::create('campaign_page', function (Blueprint $table) {
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->primary(['campaign_id', 'page_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_page');
    }
};
