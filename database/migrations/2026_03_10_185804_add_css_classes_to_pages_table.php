<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('page_css_classes', 500)->nullable()->after('icon');
            $table->string('title_css_classes', 500)->nullable()->after('page_css_classes');
            $table->string('menu_item_css_classes', 500)->nullable()->after('title_css_classes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['page_css_classes', 'title_css_classes', 'menu_item_css_classes']);
        });
    }
};
