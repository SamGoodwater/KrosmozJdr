<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Active la corbeille soft-delete pour monsters et npcs (cycle delete/restore/forceDelete).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monsters', function (Blueprint $table): void {
            if (! Schema::hasColumn('monsters', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('npcs', function (Blueprint $table): void {
            if (! Schema::hasColumn('npcs', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('monsters', function (Blueprint $table): void {
            if (Schema::hasColumn('monsters', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('npcs', function (Blueprint $table): void {
            if (Schema::hasColumn('npcs', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
