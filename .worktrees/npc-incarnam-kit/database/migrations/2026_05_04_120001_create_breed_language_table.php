<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breed_language', function (Blueprint $table) {
            $table->id();
            $table->foreignId('breed_id')->constrained('breeds')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['breed_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breed_language');
    }
};
