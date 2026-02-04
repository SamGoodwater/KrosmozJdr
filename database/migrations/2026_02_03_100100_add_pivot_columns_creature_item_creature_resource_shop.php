<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes pivot attendues par les modèles (quantity, price, comment).
     * Les tables ont été créées sans ces colonnes ; les relations withPivot() les utilisent.
     */
    public function up(): void
    {
        Schema::table('creature_item', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->after('item_id');
        });

        Schema::table('creature_resource', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->after('resource_id');
        });

        Schema::table('item_shop', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->after('shop_id');
            $table->string('price')->nullable()->after('quantity');
            $table->string('comment')->nullable()->after('price');
        });

        Schema::table('consumable_shop', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->after('shop_id');
            $table->string('price')->nullable()->after('quantity');
            $table->string('comment')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('creature_item', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
        Schema::table('creature_resource', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
        Schema::table('item_shop', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'price', 'comment']);
        });
        Schema::table('consumable_shop', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'price', 'comment']);
        });
    }
};
