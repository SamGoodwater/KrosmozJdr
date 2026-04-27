<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            if (! Schema::hasColumn('pages', 'settings')) {
                $table->json('settings')->nullable()->after('menu_item_css_classes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            if (Schema::hasColumn('pages', 'settings')) {
                $table->dropColumn('settings');
            }
        });
    }
};
