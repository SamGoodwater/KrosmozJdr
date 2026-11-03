<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mode de dépense des PA : réserve de Wakfu vs PA du tour (régénérés).
 *
 * @description
 * Capacités : défaut true (toujours depuis la réserve).
 * Sorts : défaut false (PA du tour en combat pour les sorts de classe).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capabilities', function (Blueprint $table): void {
            $table->boolean('pa_from_wakfu_reserve')
                ->default(true)
                ->after('pa');
        });

        Schema::table('spells', function (Blueprint $table): void {
            $table->boolean('pa_from_wakfu_reserve')
                ->default(false)
                ->after('pa');
        });
    }

    public function down(): void
    {
        Schema::table('capabilities', function (Blueprint $table): void {
            $table->dropColumn('pa_from_wakfu_reserve');
        });

        Schema::table('spells', function (Blueprint $table): void {
            $table->dropColumn('pa_from_wakfu_reserve');
        });
    }
};
