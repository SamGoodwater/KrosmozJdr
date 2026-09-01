<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('table_filter_presets', function (Blueprint $table) {
            if (! Schema::hasColumn('table_filter_presets', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('is_default');
            }
        });
    }

    public function down(): void
    {
        Schema::table('table_filter_presets', function (Blueprint $table) {
            if (Schema::hasColumn('table_filter_presets', 'is_public')) {
                $table->dropColumn('is_public');
            }
        });
    }
};
