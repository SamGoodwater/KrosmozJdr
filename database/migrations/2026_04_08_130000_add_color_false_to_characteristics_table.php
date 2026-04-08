<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Couleur associée à l’état « faux » des booléens (complément de icon_false).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('characteristics')) {
            return;
        }
        Schema::table('characteristics', function (Blueprint $table) {
            if (! Schema::hasColumn('characteristics', 'color_false')) {
                $table->string('color_false', 7)->nullable()->after('color');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('characteristics')) {
            return;
        }
        Schema::table('characteristics', function (Blueprint $table) {
            if (Schema::hasColumn('characteristics', 'color_false')) {
                $table->dropColumn('color_false');
            }
        });
    }
};
