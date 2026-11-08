<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Les URL d’icône Media dépassent varchar(64) (ex. /storage/images/entity/characteristics/1/…).
 *
 * @description
 * `icon` et `icon_false` stockent l’URL publique renvoyée par Spatie Media Library.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characteristics', function (Blueprint $table): void {
            $table->string('icon', 255)->nullable()->change();
            $table->string('icon_false', 255)->nullable()->comment('Icône alternative pour valeur booléenne « faux »')->change();
        });
    }

    public function down(): void
    {
        Schema::table('characteristics', function (Blueprint $table): void {
            $table->string('icon', 64)->nullable()->change();
            $table->string('icon_false', 64)->nullable()->change();
        });
    }
};
