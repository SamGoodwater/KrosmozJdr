<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('specializations', function (Blueprint $table): void {
            $table->text('short_description')->nullable()->after('name');
            $table->index('name');
            $table->index('state');
            $table->index('read_level');
            $table->index('write_level');
        });
    }

    public function down(): void
    {
        Schema::table('specializations', function (Blueprint $table): void {
            $table->dropIndex(['name']);
            $table->dropIndex(['state']);
            $table->dropIndex(['read_level']);
            $table->dropIndex(['write_level']);
            $table->dropColumn('short_description');
        });
    }
};
