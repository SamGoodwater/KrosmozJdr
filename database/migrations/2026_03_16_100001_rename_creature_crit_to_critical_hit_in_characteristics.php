<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('characteristics')
            ->where('key', 'creature_crit_creature')
            ->update(['key' => 'critical_hit_creature']);
    }

    public function down(): void
    {
        DB::table('characteristics')
            ->where('key', 'critical_hit_creature')
            ->update(['key' => 'creature_crit_creature']);
    }
};
