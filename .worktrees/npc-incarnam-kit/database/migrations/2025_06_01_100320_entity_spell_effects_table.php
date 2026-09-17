<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spell_effects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
            $table->foreignId('spell_effect_type_id')->constrained('spell_effect_types')->cascadeOnDelete();
            $table->integer('value_min')->nullable();
            $table->integer('value_max')->nullable();
            $table->unsignedTinyInteger('dice_num')->nullable();
            $table->unsignedTinyInteger('dice_side')->nullable();
            $table->unsignedSmallInteger('duration')->nullable();
            $table->string('target_scope', 16)->default('enemy');
            $table->string('zone_shape', 32)->nullable();
            $table->boolean('dispellable')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->text('raw_description')->nullable();
            $table->unsignedBigInteger('summon_monster_id')->nullable();
            $table->timestamps();

            $table->foreign('summon_monster_id')->references('id')->on('monsters')->nullOnDelete();
            $table->index(['spell_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effects');
    }
};
