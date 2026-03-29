<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('spells')) {
            return;
        }

        if (Schema::hasColumn('spells', 'auto_success_if_concentrating')) {
            Schema::table('spells', function (Blueprint $table) {
                $table->renameColumn('auto_success_if_concentrating', 'auto_success_if_willing_target');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('spells')) {
            return;
        }

        if (Schema::hasColumn('spells', 'auto_success_if_willing_target')) {
            Schema::table('spells', function (Blueprint $table) {
                $table->renameColumn('auto_success_if_willing_target', 'auto_success_if_concentrating');
            });
        }
    }
};
