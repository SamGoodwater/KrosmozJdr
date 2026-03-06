<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spell_spell_state', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->foreignId('spell_state_id')->constrained('spell_states')->cascadeOnDelete();
            $table->string('application_mode', 16)->default('target');
            $table->unsignedInteger('dofus_effect_id')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('dispellable')->nullable();
            $table->string('target_mask', 64)->nullable();
            $table->timestamps();

            $table->unique(['spell_id', 'spell_state_id', 'application_mode'], 'spell_state_mode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_spell_state');
    }
};

