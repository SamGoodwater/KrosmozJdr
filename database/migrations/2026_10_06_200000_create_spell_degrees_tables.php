<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Progression native des sorts : Spell → SpellDegree → SpellDegreeEffect.
 * Les tables Effect / EffectDegree restent pour objets et legacy jusqu’à bascule.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spell_degrees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->unsignedTinyInteger('position');
            $table->unsignedSmallInteger('required_level')->nullable();
            $table->boolean('inherits_effects')->default(true);

            // Propriétés de lancement (null = repli sur le sort).
            $table->string('pa', 64)->nullable();
            $table->string('po_min', 64)->nullable();
            $table->string('po_max', 64)->nullable();
            $table->boolean('po_editable')->nullable();
            $table->boolean('sight_line')->nullable();
            $table->boolean('cast_in_line')->nullable();
            $table->boolean('cast_in_diagonal')->nullable();
            $table->string('target_type', 16)->nullable();
            $table->integer('element')->nullable();
            $table->string('area', 64)->nullable();
            $table->string('cast_per_turn', 64)->nullable();
            $table->string('cast_per_target', 64)->nullable();
            $table->string('number_between_two_cast', 64)->nullable();
            $table->unsignedTinyInteger('global_cooldown')->nullable();
            $table->unsignedTinyInteger('max_stack')->nullable();
            $table->string('duration', 255)->nullable();
            $table->boolean('allows_reaction')->nullable();
            $table->string('casting_time', 255)->nullable();
            $table->boolean('ritual_available')->nullable();
            $table->string('resolution_mode', 32)->nullable();
            $table->string('attack_characteristic_key', 64)->nullable();
            $table->string('save_characteristic_key', 64)->nullable();
            $table->string('save_dc_formula', 255)->nullable();
            $table->text('save_success_note')->nullable();
            $table->boolean('auto_success_if_willing_target')->nullable();

            $table->timestamps();

            $table->unique(['spell_id', 'position']);
            $table->index(['spell_id', 'required_level']);
        });

        Schema::create('spell_degree_effects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spell_degree_id')->constrained('spell_degrees')->cascadeOnDelete();
            $table->foreignId('sub_effect_id')->constrained('sub_effects')->cascadeOnDelete();
            $table->unsignedSmallInteger('order')->default(0);
            $table->string('scope', 32)->default('general');
            $table->integer('value_min')->nullable();
            $table->integer('value_max')->nullable();
            $table->unsignedTinyInteger('dice_num')->nullable();
            $table->unsignedTinyInteger('dice_side')->nullable();
            $table->json('params')->nullable();
            $table->boolean('crit_only')->default(false);
            $table->string('duration_formula', 255)->nullable();
            $table->string('logic_group', 64)->nullable();
            $table->string('logic_operator', 8)->nullable();
            $table->string('logic_condition', 255)->nullable();
            $table->timestamps();

            $table->index(['spell_degree_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_degree_effects');
        Schema::dropIfExists('spell_degrees');
    }
};
