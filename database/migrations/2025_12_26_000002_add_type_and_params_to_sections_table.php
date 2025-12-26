<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @description
     * Ajoute les colonnes legacy `type` et `params` sur `sections`.
     *
     * Certaines parties du code (et les tests) utilisent encore `type/params`,
     * alors que le système moderne utilise `template/settings/data`.
     *
     * @example
     * // Legacy
     * Section::create(['type' => 'text', 'params' => ['content' => '...']]);
     * // Moderne
     * Section::create(['template' => 'text', 'settings' => [], 'data' => ['content' => '...']]);
     */
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'type')) {
                $table->string('type')->nullable()->after('template');
            }
            if (!Schema::hasColumn('sections', 'params')) {
                $table->json('params')->nullable()->after('data');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (Schema::hasColumn('sections', 'params')) {
                $table->dropColumn('params');
            }
            if (Schema::hasColumn('sections', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};


