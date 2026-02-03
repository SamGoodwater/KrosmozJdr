<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_characteristics', function (Blueprint $table) {
            $table->id();
            $table->string('entity', 32);
            $table->string('characteristic_key', 64);
            $table->string('name');
            $table->string('short_name', 64)->nullable();
            $table->text('helper')->nullable();
            $table->text('descriptions')->nullable();
            $table->string('icon', 64)->nullable();
            $table->string('color', 32)->nullable();
            $table->string('unit', 32)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('db_column', 64)->nullable();
            $table->string('type', 16)->default('string');
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->text('formula')->nullable();
            $table->text('formula_display')->nullable();
            $table->json('computation')->nullable();
            $table->string('default_value', 512)->nullable();
            $table->boolean('required')->default(false);
            $table->text('validation_message')->nullable();
            $table->boolean('forgemagie_allowed')->default(false);
            $table->unsignedTinyInteger('forgemagie_max')->default(0);
            $table->decimal('base_price_per_unit', 12, 2)->nullable();
            $table->decimal('rune_price_per_unit', 12, 2)->nullable();
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
            $table->timestamps();

            $table->unique(['entity', 'characteristic_key'], 'entity_characteristics_entity_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_characteristics');
    }
};
