<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Liaison many-to-many Classe (breed) ↔ Capacité — sans emplacement (liste plate).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breed_capability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('breed_id')->constrained('breeds')->cascadeOnDelete();
            $table->foreignId('capability_id')->constrained('capabilities')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['breed_id', 'capability_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breed_capability');
    }
};
