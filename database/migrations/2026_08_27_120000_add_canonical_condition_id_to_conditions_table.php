<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conditions', function (Blueprint $table): void {
            $table->foreignId('canonical_condition_id')
                ->nullable()
                ->after('dofusdb_id')
                ->constrained('conditions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('conditions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('canonical_condition_id');
        });
    }
};
