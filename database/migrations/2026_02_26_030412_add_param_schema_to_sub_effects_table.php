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
        Schema::table('sub_effects', function (Blueprint $table) {
            $table->json('param_schema')->nullable()->after('variables_allowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_effects', function (Blueprint $table) {
            $table->dropColumn('param_schema');
        });
    }
};
