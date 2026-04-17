<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Colonne supprimée au profit de value_overrides (entrées value: false + color).
 * Idempotent : bases déjà à jour (sans color_false) ne sont pas modifiées.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('characteristics')) {
            return;
        }
        if (! Schema::hasColumn('characteristics', 'color_false')) {
            return;
        }
        Schema::table('characteristics', function (Blueprint $table) {
            $table->dropColumn('color_false');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('characteristics')) {
            return;
        }
        if (Schema::hasColumn('characteristics', 'color_false')) {
            return;
        }
        Schema::table('characteristics', function (Blueprint $table) {
            $table->string('color_false', 32)->nullable()->after('color');
        });
    }
};
