<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table générale des caractéristiques : propriétés communes et id unique.
 * Une ligne = une caractéristique (ex. PA créature, PA sort, PA objet sont 3 lignes distinctes).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characteristics', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique()->comment('Clé unique ex: pa_creature, level_object, pa_spell');
            $table->string('name');
            $table->string('short_name', 64)->nullable();
            $table->text('helper')->nullable();
            $table->text('descriptions')->nullable();
            $table->string('icon', 64)->nullable();
            $table->string('color', 32)->nullable();
            $table->string('unit', 32)->nullable();
            $table->string('type', 16)->default('string');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characteristics');
    }
};
