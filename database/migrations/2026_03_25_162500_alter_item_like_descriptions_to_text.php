<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Les descriptions DofusDB peuvent dépasser 255 caractères (VARCHAR par défaut de string()).
     */
    public function up(): void
    {
        foreach (['items', 'resources', 'consumables'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'description')) {
                    $table->text('description')->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['items', 'resources', 'consumables'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'description')) {
                    $table->string('description')->nullable()->change();
                }
            });
        }
    }
};
