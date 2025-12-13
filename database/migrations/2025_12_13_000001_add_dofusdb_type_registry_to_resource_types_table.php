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
        Schema::table('resource_types', function (Blueprint $table) {
            // Liaison optionnelle vers un typeId DofusDB (pour pilotage allow/deny via DB)
            $table->unsignedInteger('dofusdb_type_id')->nullable()->unique()->after('name');

            // allowed|blocked|pending (pending = à valider via UX)
            $table->string('decision')->default('pending')->after('is_visible');

            // Suivi de détection
            $table->unsignedInteger('seen_count')->default(0)->after('decision');
            $table->timestamp('last_seen_at')->nullable()->after('seen_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_types', function (Blueprint $table) {
            $table->dropUnique(['dofusdb_type_id']);
            $table->dropColumn(['dofusdb_type_id', 'decision', 'seen_count', 'last_seen_at']);
        });
    }
};


