<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Icône alternative pour valeur booléenne « faux » (ex. pas de ligne de vue, portée figée).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characteristics', function (Blueprint $table) {
            $table->string('icon_false', 64)->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('characteristics', function (Blueprint $table) {
            $table->dropColumn('icon_false');
        });
    }
};
