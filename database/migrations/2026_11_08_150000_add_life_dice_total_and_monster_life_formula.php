<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part des dés de vie d'une créature + formule de gain de PV d'un monstre.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creatures', function (Blueprint $table): void {
            $table->unsignedInteger('life_dice_total')->nullable()->after('life');
        });

        Schema::table('monsters', function (Blueprint $table): void {
            $table->string('life_formula', 64)->nullable()->after('boss_pa');
        });
    }

    public function down(): void
    {
        Schema::table('creatures', function (Blueprint $table): void {
            $table->dropColumn('life_dice_total');
        });

        Schema::table('monsters', function (Blueprint $table): void {
            $table->dropColumn('life_formula');
        });
    }
};
