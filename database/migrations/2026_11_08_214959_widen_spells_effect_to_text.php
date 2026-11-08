<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Les fiches de classe (effet factuel, Pile/Face, etc.) dépassent souvent 255 caractères.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->text('effect')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->string('effect', 255)->nullable()->change();
        });
    }
};
