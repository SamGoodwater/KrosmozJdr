<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Supprime la table legacy `spell_effects` (canal canon = Effect / effect_spell).
 * Refuse de dropper si des lignes existent encore (sécurité).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('spell_effects')) {
            return;
        }

        $count = (int) DB::table('spell_effects')->count();
        if ($count > 0) {
            throw new \RuntimeException(
                "Refus de dropper spell_effects : {$count} ligne(s) restantes. Migrer vers Effect avant."
            );
        }

        Schema::drop('spell_effects');
    }

    public function down(): void
    {
        if (Schema::hasTable('spell_effects')) {
            return;
        }

        Schema::create('spell_effects', function (Blueprint $table): void {
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
};
