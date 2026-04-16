<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lie une section CMS (template texte) comme aide détaillée sous les normes d’une caractéristique.
 */
return new class extends Migration
{
    /** @var list<string> */
    private const TABLES = [
        'characteristic_creature',
        'characteristic_object',
        'characteristic_spell',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('norms_help_section_id')
                    ->nullable()
                    ->after('norms_description')
                    ->constrained('sections')
                    ->nullOnDelete()
                    ->comment('Section texte riche (CMS) affichée sous la charte');
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropConstrainedForeignId('norms_help_section_id');
            });
        }
    }
};
