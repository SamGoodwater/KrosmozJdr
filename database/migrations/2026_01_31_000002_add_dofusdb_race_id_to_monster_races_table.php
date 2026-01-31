<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monster_races', function (Blueprint $table) {
            if (!Schema::hasColumn('monster_races', 'dofusdb_race_id')) {
                $table->integer('dofusdb_race_id')->nullable()->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('monster_races', function (Blueprint $table) {
            if (Schema::hasColumn('monster_races', 'dofusdb_race_id')) {
                $table->dropUnique(['dofusdb_race_id']);
                $table->dropColumn('dofusdb_race_id');
            }
        });
    }
};

