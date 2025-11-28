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
        Schema::table('panoplies', function (Blueprint $table) {
            $table->string('dofusdb_id')->nullable()->after('id');
            $table->index('dofusdb_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panoplies', function (Blueprint $table) {
            $table->dropIndex(['dofusdb_id']);
            $table->dropColumn('dofusdb_id');
        });
    }
};
