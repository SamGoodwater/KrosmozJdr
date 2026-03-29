<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sort sans élément : valeur NULL (distincte du primaire Neutre = 0).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            $table->integer('element')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            $table->integer('element')->default(0)->nullable(false)->change();
        });
    }
};
