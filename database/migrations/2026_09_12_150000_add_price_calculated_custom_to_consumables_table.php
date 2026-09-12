<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->bigInteger('price_calculated')->nullable()->after('recipe');
            $table->bigInteger('price_custom')->nullable()->after('price_calculated');
        });
    }

    public function down(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->dropColumn(['price_calculated', 'price_custom']);
        });
    }
};
