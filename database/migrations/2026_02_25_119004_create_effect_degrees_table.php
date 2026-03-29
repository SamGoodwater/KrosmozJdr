<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un degré d’effet : zone, slug technique, seuil de niveau requis, sous-effets (pivot effect_sub_effect).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('effect_degrees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('effect_id')->constrained('effects')->cascadeOnDelete();
            $table->unsignedTinyInteger('degree');
            $table->unsignedSmallInteger('required_creature_level')->nullable();
            $table->string('area', 64)->nullable();
            $table->string('slug', 64)->nullable()->unique();
            $table->string('config_signature', 64)->nullable();
            $table->timestamps();

            $table->unique(['effect_id', 'degree']);
            $table->index('config_signature');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_degrees');
    }
};
