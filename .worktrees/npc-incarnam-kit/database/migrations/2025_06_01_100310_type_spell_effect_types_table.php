<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spell_effect_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 64)->unique();
            $table->string('category', 32);
            $table->text('description')->nullable();
            $table->string('value_type', 16)->default('fixed');
            $table->string('element', 16)->nullable();
            $table->string('unit', 32)->nullable();
            $table->boolean('is_positive')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unsignedInteger('dofusdb_effect_id')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_types');
    }
};
