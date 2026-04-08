<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alignement avec characteristic_spell (casting_time_spell, ritual_available_spell).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('spells')) {
            return;
        }
        Schema::table('spells', function (Blueprint $table) {
            if (! Schema::hasColumn('spells', 'casting_time')) {
                $table->string('casting_time', 255)->nullable()->after('pa');
            }
            if (! Schema::hasColumn('spells', 'ritual_available')) {
                $table->boolean('ritual_available')->nullable()->after('casting_time');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('spells')) {
            return;
        }
        Schema::table('spells', function (Blueprint $table) {
            if (Schema::hasColumn('spells', 'ritual_available')) {
                $table->dropColumn('ritual_available');
            }
            if (Schema::hasColumn('spells', 'casting_time')) {
                $table->dropColumn('casting_time');
            }
        });
    }
};
