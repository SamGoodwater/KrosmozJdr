<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('characteristics', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('db_column', 64)->nullable();
            $table->string('name');
            $table->string('short_name', 64)->nullable();
            $table->text('description')->nullable();
            $table->string('type', 16);
            $table->string('unit', 32)->nullable();
            $table->string('icon', 64)->nullable();
            $table->string('color', 32)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('forgemagie_allowed')->default(false);
            $table->unsignedTinyInteger('forgemagie_max')->default(0);
            $table->json('applies_to')->nullable();
            $table->boolean('is_competence')->default(false);
            $table->string('characteristic_id', 64)->nullable();
            $table->string('alternative_characteristic_id', 64)->nullable();
            $table->string('skill_type', 32)->nullable();
            $table->json('value_available')->nullable();
            $table->json('labels')->nullable();
            $table->json('validation')->nullable();
            $table->json('mastery_value_available')->nullable();
            $table->json('mastery_labels')->nullable();
            $table->decimal('base_price_per_unit', 12, 2)->nullable();
            $table->decimal('rune_price_per_unit', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('characteristic_id')->references('id')->on('characteristics')->nullOnDelete();
            $table->foreign('alternative_characteristic_id')->references('id')->on('characteristics')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characteristics');
    }
};
