<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Retire les colonnes créature liées aux caractéristiques abandonnées
 * (compétences artisanat/herbaliste/connaissance, dommages/résistances sag-vit).
 */
return new class extends Migration
{
    /** @var list<string> */
    private const COLUMNS = [
        'do_sagesse',
        'do_vitalite',
        'do_sagesse_context',
        'do_vitalite_context',
        'res_sagesse',
        'res_vitalite',
        'res_sagesse_context',
        'res_vitalite_context',
        'artisanat_bonus',
        'herbaliste_bonus',
        'connaissance_creatures_bonus',
        'artisanat_mastery',
        'herbaliste_mastery',
        'connaissance_creatures_mastery',
    ];

    public function up(): void
    {
        Schema::table('creatures', function (Blueprint $table): void {
            foreach (self::COLUMNS as $column) {
                if (Schema::hasColumn('creatures', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('creatures', function (Blueprint $table): void {
            if (! Schema::hasColumn('creatures', 'do_sagesse')) {
                $table->string('do_sagesse')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'do_vitalite')) {
                $table->string('do_vitalite')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'do_sagesse_context')) {
                $table->string('do_sagesse_context')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'do_vitalite_context')) {
                $table->string('do_vitalite_context')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'res_sagesse')) {
                $table->string('res_sagesse')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'res_vitalite')) {
                $table->string('res_vitalite')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'res_sagesse_context')) {
                $table->string('res_sagesse_context')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'res_vitalite_context')) {
                $table->string('res_vitalite_context')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'artisanat_bonus')) {
                $table->string('artisanat_bonus')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'herbaliste_bonus')) {
                $table->string('herbaliste_bonus')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'connaissance_creatures_bonus')) {
                $table->string('connaissance_creatures_bonus')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'artisanat_mastery')) {
                $table->unsignedTinyInteger('artisanat_mastery')->default(0);
            }
            if (! Schema::hasColumn('creatures', 'herbaliste_mastery')) {
                $table->unsignedTinyInteger('herbaliste_mastery')->default(0);
            }
            if (! Schema::hasColumn('creatures', 'connaissance_creatures_mastery')) {
                $table->unsignedTinyInteger('connaissance_creatures_mastery')->default(0);
            }
        });
    }
};
