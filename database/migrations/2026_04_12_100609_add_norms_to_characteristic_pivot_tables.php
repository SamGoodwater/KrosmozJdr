<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les colonnes de normes (chartes) aux trois tables pivot de caractéristiques.
 * Grille 5 puissances × 20 niveaux, conditions de lecture et description libre.
 */
return new class extends Migration
{
    /** @var list<string> */
    private const TABLES = [
        'characteristic_creature',
        'characteristic_object',
        'characteristic_spell',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->json('norms_grid')->nullable()->after('conversion_sample_rows')
                    ->comment('Grille 5×20 : {power_level: [val_lvl1..val_lvl20]}');
                $blueprint->json('norms_conditions')->nullable()->after('norms_grid')
                    ->comment('Conditions de lecture : [{characteristic_key, operator, value, target, modifier, comment}]');
                $blueprint->text('norms_description')->nullable()->after('norms_conditions')
                    ->comment('Description libre de la norme');
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn(['norms_grid', 'norms_conditions', 'norms_description']);
            });
        }
    }
};
