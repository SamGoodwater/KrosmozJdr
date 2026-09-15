<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            $table->string('official_id')->nullable()->unique()->after('creature_id');
            $table->boolean('auto_update')->default(true)->after('official_id');
        });
    }

    public function down(): void
    {
        Schema::table('npcs', function (Blueprint $table) {
            $table->dropUnique(['official_id']);
            $table->dropColumn(['official_id', 'auto_update']);
        });
    }
};
