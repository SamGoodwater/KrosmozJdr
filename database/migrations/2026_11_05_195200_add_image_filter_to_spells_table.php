<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Filtre CSS d’icône pour un sort devenu basique (beige / violet / personnalisé).
 *
 * @description
 * JSON nullable : null = image d’origine ; {preset:beige|violet} = préréglage ;
 * {preset:custom, grayscale, sepia, hue, saturate, brightness, contrast} = réglages libres.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->json('image_filter')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table): void {
            $table->dropColumn('image_filter');
        });
    }
};
