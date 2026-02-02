<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Déplace forgemagie et prix de la table characteristics vers characteristic_entities
     * (uniquement pour l'entité "item" / équipement).
     */
    public function up(): void
    {
        Schema::table('characteristic_entities', function (Blueprint $table) {
            $table->boolean('forgemagie_allowed')->default(false)->after('validation_message');
            $table->unsignedTinyInteger('forgemagie_max')->default(0)->after('forgemagie_allowed');
            $table->decimal('base_price_per_unit', 12, 2)->nullable()->after('forgemagie_max');
            $table->decimal('rune_price_per_unit', 12, 2)->nullable()->after('base_price_per_unit');
        });

        $chars = DB::table('characteristics')->select(
            'id',
            'forgemagie_allowed',
            'forgemagie_max',
            'base_price_per_unit',
            'rune_price_per_unit'
        )->get();

        foreach ($chars as $c) {
            $itemEntity = DB::table('characteristic_entities')
                ->where('characteristic_id', $c->id)
                ->where('entity', 'item')
                ->first();

            if ($itemEntity) {
                DB::table('characteristic_entities')
                    ->where('id', $itemEntity->id)
                    ->update([
                        'forgemagie_allowed' => (bool) $c->forgemagie_allowed,
                        'forgemagie_max' => (int) $c->forgemagie_max,
                        'base_price_per_unit' => $c->base_price_per_unit,
                        'rune_price_per_unit' => $c->rune_price_per_unit,
                    ]);
            } else {
                DB::table('characteristic_entities')->insert([
                    'characteristic_id' => $c->id,
                    'entity' => 'item',
                    'forgemagie_allowed' => (bool) $c->forgemagie_allowed,
                    'forgemagie_max' => (int) $c->forgemagie_max,
                    'base_price_per_unit' => $c->base_price_per_unit,
                    'rune_price_per_unit' => $c->rune_price_per_unit,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('characteristics', function (Blueprint $table) {
            $table->dropColumn([
                'forgemagie_allowed',
                'forgemagie_max',
                'base_price_per_unit',
                'rune_price_per_unit',
            ]);
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('characteristics', function (Blueprint $table) {
            $table->boolean('forgemagie_allowed')->default(false)->after('sort_order');
            $table->unsignedTinyInteger('forgemagie_max')->default(0)->after('forgemagie_allowed');
            $table->decimal('base_price_per_unit', 12, 2)->nullable()->after('mastery_labels');
            $table->decimal('rune_price_per_unit', 12, 2)->nullable()->after('base_price_per_unit');
        });

        $chars = DB::table('characteristics')->select('id')->get();
        foreach ($chars as $c) {
            $itemEntity = DB::table('characteristic_entities')
                ->where('characteristic_id', $c->id)
                ->where('entity', 'item')
                ->first();
            if ($itemEntity) {
                DB::table('characteristics')->where('id', $c->id)->update([
                    'forgemagie_allowed' => (bool) $itemEntity->forgemagie_allowed,
                    'forgemagie_max' => (int) $itemEntity->forgemagie_max,
                    'base_price_per_unit' => $itemEntity->base_price_per_unit,
                    'rune_price_per_unit' => $itemEntity->rune_price_per_unit,
                ]);
            }
        }

        Schema::table('characteristic_entities', function (Blueprint $table) {
            $table->dropColumn([
                'forgemagie_allowed',
                'forgemagie_max',
                'base_price_per_unit',
                'rune_price_per_unit',
            ]);
        });
    }
};
