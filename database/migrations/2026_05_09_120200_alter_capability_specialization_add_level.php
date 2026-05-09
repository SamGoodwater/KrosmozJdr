<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capability_specialization', function (Blueprint $table): void {
            if (! Schema::hasColumn('capability_specialization', 'level')) {
                $table->unsignedSmallInteger('level')->default(1);
            }
            if (! Schema::hasColumn('capability_specialization', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('capability_specialization', function (Blueprint $table): void {
            if (Schema::hasColumn('capability_specialization', 'created_at')) {
                $table->dropColumn(['created_at', 'updated_at']);
            }
            if (Schema::hasColumn('capability_specialization', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};
