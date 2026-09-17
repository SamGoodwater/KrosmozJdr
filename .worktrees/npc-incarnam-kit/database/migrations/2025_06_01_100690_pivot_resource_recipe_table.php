<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_recipe', function (Blueprint $table) {
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('ingredient_resource_id')->constrained('resources')->cascadeOnDelete();
            $table->string('quantity')->default('1');
            $table->primary(['resource_id', 'ingredient_resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_recipe');
    }
};
