<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Compétences techniques (Artisanat, Herbaliste, Connaissance des créatures) + choix Force/Chance (Intimidation).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creatures', function (Blueprint $table): void {
            $table->string('artisanat_bonus')->default('0')->after('supercherie_bonus');
            $table->string('herbaliste_bonus')->default('0')->after('artisanat_bonus');
            $table->string('connaissance_creatures_bonus')->default('0')->after('herbaliste_bonus');

            $table->tinyInteger('artisanat_mastery')->default(0)->after('supercherie_mastery');
            $table->tinyInteger('herbaliste_mastery')->default(0)->after('artisanat_mastery');
            $table->tinyInteger('connaissance_creatures_mastery')->default(0)->after('herbaliste_mastery');

            $table->string('intimidation_ability', 16)->default('strength')->after('intimidation_mastery');
        });
    }

    public function down(): void
    {
        Schema::table('creatures', function (Blueprint $table): void {
            $table->dropColumn([
                'artisanat_bonus',
                'herbaliste_bonus',
                'connaissance_creatures_bonus',
                'artisanat_mastery',
                'herbaliste_mastery',
                'connaissance_creatures_mastery',
                'intimidation_ability',
            ]);
        });
    }
};
