<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Prix objet : part calculée (recette + bonus caractéristiques) et part personnalisée (DofusDB, ajustement MJ).
 * La colonne `price` existante conserve le total affiché (kamas entiers, sans centimes).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('items')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'price_calculated')) {
                $table->bigInteger('price_calculated')->nullable()->after('recipe');
            }
            if (! Schema::hasColumn('items', 'price_custom')) {
                $table->bigInteger('price_custom')->nullable()->after('price_calculated');
            }
        });

        DB::table('items')->orderBy('id')->chunkById(100, function ($rows): void {
            foreach ($rows as $row) {
                $raw = $row->price ?? null;
                $parsed = is_numeric($raw) ? (int) $raw : 0;
                $calc = null;
                $custom = $parsed;
                $total = max(0, (int) ($calc ?? 0) + $custom);
                DB::table('items')->where('id', $row->id)->update([
                    'price_calculated' => $calc,
                    'price_custom' => $custom,
                    'price' => (string) $total,
                ]);
            }
        }, 'id');
    }

    public function down(): void
    {
        if (! Schema::hasTable('items')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'price_custom')) {
                $table->dropColumn('price_custom');
            }
            if (Schema::hasColumn('items', 'price_calculated')) {
                $table->dropColumn('price_calculated');
            }
        });
    }
};
