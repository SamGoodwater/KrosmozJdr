<?php

declare(strict_types=1);

use App\Support\CatalogTypeVisibility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Flag catalogue (booléen nullable, défaut applicatif via Eloquent `$attributes`).
 *
 * Pas de DEFAULT SQL : MariaDB/MySQL refuse certains défauts, et on évite un
 * DEFAULT 0 implicite lors d’un CHANGE NOT NULL.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['item_types', 'resource_types', 'consumable_types'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->boolean('show_in_catalog')->nullable();
            });
        }

        $this->backfill('item_types', CatalogTypeVisibility::ITEM_DOFUS_IDS);
        $this->backfill('resource_types', CatalogTypeVisibility::RESOURCE_DOFUS_IDS);
        $this->backfill('consumable_types', CatalogTypeVisibility::CONSUMABLE_DOFUS_IDS);
    }

    public function down(): void
    {
        foreach (['item_types', 'resource_types', 'consumable_types'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('show_in_catalog');
            });
        }
    }

    /**
     * @param  list<int>  $dofus_ids
     */
    private function backfill(string $table, array $dofus_ids): void
    {
        DB::table($table)->whereNull('show_in_catalog')->update(['show_in_catalog' => 0]);

        if ($dofus_ids === []) {
            return;
        }

        DB::table($table)
            ->whereIn('dofusdb_type_id', $dofus_ids)
            ->update(['show_in_catalog' => 1]);
    }
};
