<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Surcharges visuelles par valeur : icône, couleur et sous-texte conditionnels.
 *
 * Chaque entrée du JSON est un objet { value, icon?, color?, subtitle? }.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('characteristics')) {
            return;
        }
        Schema::table('characteristics', function (Blueprint $table) {
            if (! Schema::hasColumn('characteristics', 'value_overrides')) {
                $table->json('value_overrides')->nullable()->after('color');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('characteristics')) {
            return;
        }
        Schema::table('characteristics', function (Blueprint $table) {
            if (Schema::hasColumn('characteristics', 'value_overrides')) {
                $table->dropColumn('value_overrides');
            }
        });
    }
};
