<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conditions', function (Blueprint $table): void {
            $table->boolean('dissipable')->default(true)->after('is_main_state');
        });
    }

    public function down(): void
    {
        Schema::table('conditions', function (Blueprint $table): void {
            $table->dropColumn('dissipable');
        });
    }
};
