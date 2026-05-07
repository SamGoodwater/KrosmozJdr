<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breed_creature_trait', function (Blueprint $table): void {
            $table->foreignId('breed_id')->constrained('breeds')->cascadeOnDelete();
            $table->foreignId('creature_trait_id')->constrained('creature_traits')->cascadeOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->primary(['breed_id', 'creature_trait_id']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('breed_creature_trait'); }
};
