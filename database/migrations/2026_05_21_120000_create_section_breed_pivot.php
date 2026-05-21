<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_breed', function (Blueprint $table): void {
            $table->foreignId('breed_id')->constrained('breeds')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->timestamps();
            $table->primary(['breed_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_breed');
    }
};
