<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Les états scrapés (dofusdb_id) en brouillon passent en brut.
     * Les fiches JDR sans id DofusDB (seeder Pesanteur, etc.) restent inchangées.
     */
    public function up(): void
    {
        DB::table('conditions')
            ->whereNotNull('dofusdb_id')
            ->where('state', 'draft')
            ->update(['state' => 'raw']);
    }

    public function down(): void
    {
        DB::table('conditions')
            ->whereNotNull('dofusdb_id')
            ->where('state', 'raw')
            ->update(['state' => 'draft']);
    }
};
