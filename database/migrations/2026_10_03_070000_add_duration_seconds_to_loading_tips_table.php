<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loading_tips', function (Blueprint $table) {
            $table->unsignedTinyInteger('duration_seconds')->default(8)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('loading_tips', function (Blueprint $table) {
            $table->dropColumn('duration_seconds');
        });
    }
};
