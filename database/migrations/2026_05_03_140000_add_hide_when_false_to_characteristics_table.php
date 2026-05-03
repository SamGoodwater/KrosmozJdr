<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Masque la ligne d’affichage lorsque la valeur booléenne est fausse (ex. is_passive).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('characteristics')) {
            return;
        }
        if (! Schema::hasColumn('characteristics', 'hide_when_false')) {
            Schema::table('characteristics', function (Blueprint $table) {
                $table->boolean('hide_when_false')->default(false)->after('hide_when_empty');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('characteristics')) {
            return;
        }
        if (Schema::hasColumn('characteristics', 'hide_when_false')) {
            Schema::table('characteristics', function (Blueprint $table) {
                $table->dropColumn('hide_when_false');
            });
        }
    }
};
