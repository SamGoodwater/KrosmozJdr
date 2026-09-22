<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aligne consommables / ressources sur les équipements : bonus JSON filtrable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->text('bonus')->nullable()->after('effect');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->text('bonus')->nullable()->after('effect');
        });
    }

    public function down(): void
    {
        Schema::table('consumables', function (Blueprint $table) {
            $table->dropColumn('bonus');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('bonus');
        });
    }
};
