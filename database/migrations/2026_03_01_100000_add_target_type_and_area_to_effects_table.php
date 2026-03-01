<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute target_type (direct / piège / glyphe) et area (notation zone sur damier).
     *
     * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
     */
    public function up(): void
    {
        Schema::table('effects', function (Blueprint $table) {
            $table->string('target_type', 32)->default('direct')->after('degree');
            $table->string('area', 64)->nullable()->after('target_type');
        });
    }

    public function down(): void
    {
        Schema::table('effects', function (Blueprint $table) {
            $table->dropColumn(['target_type', 'area']);
        });
    }
};
