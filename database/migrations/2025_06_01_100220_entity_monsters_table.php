<?php

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
        Schema::create('monsters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creature_id')->nullable()->constrained('creatures')->cascadeOnDelete();
            $table->string('official_id')->nullable();
            $table->string('dofusdb_id')->nullable();
            $table->string('dofus_version')->default('3');
            $table->boolean('auto_update')->default(true);
            $table->integer('size')->default(2);
            $table->boolean('is_boss')->default(false);
            $table->string('boss_pa')->default('');
            $table->foreignId('monster_race_id')->nullable()->constrained('monster_races')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monsters');
    }
};
