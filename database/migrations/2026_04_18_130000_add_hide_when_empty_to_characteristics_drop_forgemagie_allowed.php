<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - hide_when_empty : masquer la ligne en jeu lorsque la valeur est vide / nulle / zéro (selon règles UI).
 * - forgemagie_allowed supprimé : équivalent à forgemagie_max > 0.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('characteristics') && ! Schema::hasColumn('characteristics', 'hide_when_empty')) {
            Schema::table('characteristics', function (Blueprint $table) {
                $table->boolean('hide_when_empty')->default(false);
            });
        }

        if (Schema::hasTable('characteristic_object') && Schema::hasColumn('characteristic_object', 'forgemagie_allowed')) {
            Schema::table('characteristic_object', function (Blueprint $table) {
                $table->dropColumn('forgemagie_allowed');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('characteristics') && Schema::hasColumn('characteristics', 'hide_when_empty')) {
            Schema::table('characteristics', function (Blueprint $table) {
                $table->dropColumn('hide_when_empty');
            });
        }

        if (Schema::hasTable('characteristic_object') && ! Schema::hasColumn('characteristic_object', 'forgemagie_allowed')) {
            Schema::table('characteristic_object', function (Blueprint $table) {
                $table->boolean('forgemagie_allowed')->default(false)->after('conversion_sample_rows');
            });
        }
    }
};
