<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monster_language', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monster_id')->constrained('monsters')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['monster_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monster_language');
    }
};
