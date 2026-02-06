<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['characteristic_creature', 'characteristic_object', 'characteristic_spell'];
        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} MODIFY min VARCHAR(512) NULL");
            DB::statement("ALTER TABLE {$table} MODIFY max VARCHAR(512) NULL");
        }
    }

    public function down(): void
    {
        $tables = ['characteristic_creature', 'characteristic_object', 'characteristic_spell'];
        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} MODIFY min INT NULL");
            DB::statement("ALTER TABLE {$table} MODIFY max INT NULL");
        }
    }
};
