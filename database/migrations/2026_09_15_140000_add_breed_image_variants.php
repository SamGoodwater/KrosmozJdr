<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('breeds', function (Blueprint $table) {
            $table->string('symbol_full')->nullable()->after('icon');
            $table->string('symbol_bw')->nullable()->after('symbol_full');
            $table->string('logo_male')->nullable()->after('symbol_bw');
            $table->string('logo_female')->nullable()->after('logo_male');
            $table->string('image_full_male')->nullable()->after('logo_female');
            $table->string('image_full_female')->nullable()->after('image_full_male');
        });
    }

    public function down(): void
    {
        Schema::table('breeds', function (Blueprint $table) {
            $table->dropColumn([
                'symbol_full',
                'symbol_bw',
                'logo_male',
                'logo_female',
                'image_full_male',
                'image_full_female',
            ]);
        });
    }
};
