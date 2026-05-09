<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialization_spell', function (Blueprint $table): void {
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->timestamps();
            $table->primary(['specialization_id', 'spell_id']);
        });

        Schema::create('consumable_specialization', function (Blueprint $table): void {
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->foreignId('consumable_id')->constrained('consumables')->cascadeOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->primary(['specialization_id', 'consumable_id']);
        });

        Schema::create('resource_specialization', function (Blueprint $table): void {
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->primary(['specialization_id', 'resource_id']);
        });

        Schema::create('item_specialization', function (Blueprint $table): void {
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->primary(['specialization_id', 'item_id']);
        });

        Schema::create('section_specialization', function (Blueprint $table): void {
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->timestamps();
            $table->primary(['specialization_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_specialization');
        Schema::dropIfExists('item_specialization');
        Schema::dropIfExists('resource_specialization');
        Schema::dropIfExists('consumable_specialization');
        Schema::dropIfExists('specialization_spell');
    }
};
