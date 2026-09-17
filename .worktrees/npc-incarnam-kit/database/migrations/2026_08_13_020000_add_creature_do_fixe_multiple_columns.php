<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Branche le Dommage fixe Multiples (DO mult.) sur une colonne composable créature.
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creatures', function (Blueprint $table): void {
            if (! Schema::hasColumn('creatures', 'do_fixe_multiple')) {
                $table->text('do_fixe_multiple')->nullable();
            }
            if (! Schema::hasColumn('creatures', 'do_fixe_multiple_context')) {
                $table->text('do_fixe_multiple_context')->nullable();
            }
        });

        // Relie la définition seed sans reseed global (préserve les autres customisations).
        $characteristicId = DB::table('characteristics')
            ->where('key', 'fixed_damage_multiple_creature')
            ->value('id');

        if ($characteristicId !== null) {
            DB::table('characteristic_creature')
                ->where('characteristic_id', $characteristicId)
                ->where(function ($query): void {
                    $query->whereNull('db_column')->orWhere('db_column', '');
                })
                ->update([
                    'db_column' => 'do_fixe_multiple',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $characteristicId = DB::table('characteristics')
            ->where('key', 'fixed_damage_multiple_creature')
            ->value('id');

        if ($characteristicId !== null) {
            DB::table('characteristic_creature')
                ->where('characteristic_id', $characteristicId)
                ->where('db_column', 'do_fixe_multiple')
                ->update([
                    'db_column' => null,
                    'updated_at' => now(),
                ]);
        }

        Schema::table('creatures', function (Blueprint $table): void {
            $drop = [];
            if (Schema::hasColumn('creatures', 'do_fixe_multiple_context')) {
                $drop[] = 'do_fixe_multiple_context';
            }
            if (Schema::hasColumn('creatures', 'do_fixe_multiple')) {
                $drop[] = 'do_fixe_multiple';
            }
            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
