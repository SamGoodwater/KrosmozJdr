<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Bootstrap des typeId DofusDB "ressource" connus pour démarrer le sync catalogue.
     *
     * @return void
     */
    public function up(): void
    {
        // DofusDB:
        // - 15: resources (matériaux)
        // - 35: flowers
        $defaults = [
            [
                'dofusdb_type_id' => 15,
                'name' => 'Ressource (DofusDB)',
                'decision' => 'allowed',
                'usable' => 1,
                'is_visible' => 'guest',
                'seen_count' => 0,
                'last_seen_at' => now(),
            ],
            [
                'dofusdb_type_id' => 35,
                'name' => 'Fleur (DofusDB)',
                'decision' => 'allowed',
                'usable' => 1,
                'is_visible' => 'guest',
                'seen_count' => 0,
                'last_seen_at' => now(),
            ],
        ];

        foreach ($defaults as $row) {
            DB::table('resource_types')->updateOrInsert(
                ['dofusdb_type_id' => $row['dofusdb_type_id']],
                $row
            );
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        DB::table('resource_types')
            ->whereIn('dofusdb_type_id', [15, 35])
            ->delete();
    }
};


