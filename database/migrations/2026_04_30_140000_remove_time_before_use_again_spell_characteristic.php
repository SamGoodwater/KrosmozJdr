<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Supprime la caractéristique spell « temps avant réutilisation » : non présente sur la table `spells`.
 * Pour les sorts, le délai entre deux lancers est `number_between_two_cast` (`number_between_two_cast_spell`).
 * Les capacités conservent `capabilities.time_before_use_again` sans passer par cette caractéristique.
 */
return new class extends Migration
{
    private const CHARACTERISTIC_KEY = 'time_before_use_again_spell';

    public function up(): void
    {
        $row = DB::table('characteristics')->where('key', self::CHARACTERISTIC_KEY)->first();
        if ($row === null) {
            return;
        }

        DB::table('characteristic_spell')->where('characteristic_id', $row->id)->delete();
        DB::table('characteristics')->where('id', $row->id)->delete();
    }

    public function down(): void
    {
        // Réintroduction manuelle via fichier JSON versionné + `php artisan db:seed --class=CharacteristicSeeder`.
    }
};
