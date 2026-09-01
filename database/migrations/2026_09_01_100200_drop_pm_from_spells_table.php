<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            if (Schema::hasColumn('spells', 'pm')) {
                $table->dropColumn('pm');
            }
        });
    }

    public function down(): void
    {
        // Les sorts n’ont pas de coût PM ; colonne retirée volontairement.
    }
};
