<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Harmonise les registres de types : `show_in_catalog` (visible / tableaux)
 * et `allow_scrap` (maj DofusDB). Pas de DEFAULT SQL (MariaDB/MySQL 8).
 */
return new class extends Migration
{
    /** @var list<string> */
    private const OBJECT_TABLES = ['item_types', 'resource_types', 'consumable_types'];

    /** @var list<string> */
    private const INTERNAL_TABLES = ['monster_races', 'spell_types'];

    public function up(): void
    {
        foreach (self::OBJECT_TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->boolean('allow_scrap')->nullable();
            });

            DB::table($table)->update(['allow_scrap' => 0]);
            DB::table($table)->where('decision', 'allowed')->update(['allow_scrap' => 1]);
        }

        foreach (self::INTERNAL_TABLES as $table) {
            $needsCatalog = ! Schema::hasColumn($table, 'show_in_catalog');
            Schema::table($table, function (Blueprint $blueprint) use ($needsCatalog) {
                if ($needsCatalog) {
                    $blueprint->boolean('show_in_catalog')->nullable();
                }
                $blueprint->boolean('allow_scrap')->nullable();
            });

            DB::table($table)->update([
                'show_in_catalog' => 0,
                'allow_scrap' => 0,
            ]);
            DB::table($table)->where('state', 'playable')->update([
                'show_in_catalog' => 1,
                'allow_scrap' => 1,
            ]);
        }
    }

    public function down(): void
    {
        foreach (self::OBJECT_TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('allow_scrap');
            });
        }

        foreach (self::INTERNAL_TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropColumn('allow_scrap');
                if (Schema::hasColumn($table, 'show_in_catalog')) {
                    $blueprint->dropColumn('show_in_catalog');
                }
            });
        }
    }
};
