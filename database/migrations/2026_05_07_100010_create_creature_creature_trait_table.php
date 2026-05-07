<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creature_creature_trait', function (Blueprint $table): void {
            $table->foreignId('creature_id')->constrained('creatures')->cascadeOnDelete();
            $table->foreignId('creature_trait_id')->constrained('creature_traits')->cascadeOnDelete();
            $table->primary(['creature_id', 'creature_trait_id']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('creature_creature_trait'); }
};
