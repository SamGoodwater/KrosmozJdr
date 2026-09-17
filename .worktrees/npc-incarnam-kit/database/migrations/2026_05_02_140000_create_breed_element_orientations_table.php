<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breed_element_orientations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('breed_id')->constrained('breeds')->cascadeOnDelete();
            $table->string('element', 16);
            $table->string('orientation_key', 64);
            $table->timestamps();

            $table->unique(['breed_id', 'element']);
            $table->index('element');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breed_element_orientations');
    }
};
