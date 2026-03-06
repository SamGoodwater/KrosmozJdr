<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spell_states', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('dofusdb_id')->unique();
            $table->string('name')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();

            $table->boolean('prevents_spell_cast')->default(false);
            $table->boolean('prevents_fight')->default(false);
            $table->boolean('cant_be_moved')->default(false);
            $table->boolean('cant_be_pushed')->default(false);
            $table->boolean('cant_deal_damage')->default(false);
            $table->boolean('invulnerable')->default(false);
            $table->boolean('cant_switch_position')->default(false);
            $table->boolean('incurable')->default(false);
            $table->boolean('invulnerable_melee')->default(false);
            $table->boolean('invulnerable_range')->default(false);
            $table->boolean('cant_tackle')->default(false);
            $table->boolean('cant_be_tackled')->default(false);
            $table->boolean('display_turn_remaining')->default(false);
            $table->boolean('is_main_state')->default(false);

            $table->json('raw')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_states');
    }
};

