<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('condition_spell', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained('conditions')->cascadeOnDelete();
            $table->string('application_mode', 16)->default('target');
            $table->unsignedInteger('dofus_effect_id')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('dispellable')->nullable();
            $table->string('target_mask', 64)->nullable();
            $table->timestamps();
            $table->index(['spell_id', 'condition_id', 'application_mode'], 'condition_spell_mode_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condition_spell');
    }
};
