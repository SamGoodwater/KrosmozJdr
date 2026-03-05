<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monsters', function (Blueprint $table) {
            $table->string('state')->default('draft')->after('monster_race_id');
            $table->tinyInteger('read_level')->default(0)->after('state');
            $table->tinyInteger('write_level')->default(3)->after('read_level');
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->string('state')->default('draft')->after('specialization_id');
            $table->tinyInteger('read_level')->default(0)->after('state');
            $table->tinyInteger('write_level')->default(3)->after('read_level');
        });
    }

    public function down(): void
    {
        Schema::table('monsters', function (Blueprint $table) {
            $table->dropColumn(['state', 'read_level', 'write_level']);
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->dropColumn(['state', 'read_level', 'write_level']);
        });
    }
};
