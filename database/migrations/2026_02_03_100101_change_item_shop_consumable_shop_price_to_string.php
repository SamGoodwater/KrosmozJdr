<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aligne le type de price/comment sur resource_shop (string) pour item_shop et consumable_shop.
     */
    public function up(): void
    {
        Schema::table('item_shop', function (Blueprint $table) {
            $table->string('price')->nullable()->change();
        });
        Schema::table('consumable_shop', function (Blueprint $table) {
            $table->string('price')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('item_shop', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->change();
        });
        Schema::table('consumable_shop', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->change();
        });
    }
};
