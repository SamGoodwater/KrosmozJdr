<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sort utilisable comme réaction de combat (PA non récupérés au tour suivant).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('spells')) {
            return;
        }
        if (Schema::hasColumn('spells', 'allows_reaction')) {
            return;
        }
        Schema::table('spells', function (Blueprint $table) {
            $table->boolean('allows_reaction')->default(false)->after('auto_success_if_willing_target');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('spells') || ! Schema::hasColumn('spells', 'allows_reaction')) {
            return;
        }
        Schema::table('spells', function (Blueprint $table) {
            $table->dropColumn('allows_reaction');
        });
    }
};
