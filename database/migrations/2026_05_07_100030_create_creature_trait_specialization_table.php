<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creature_trait_specialization', function (Blueprint $table): void {
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->foreignId('creature_trait_id')->constrained('creature_traits')->cascadeOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->primary(['specialization_id', 'creature_trait_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_trait_specialization');
    }
};
