<?php

declare(strict_types=1);

use App\Support\Creature\CreatureComposableColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les colonnes de bonus contextuel et rend les totaux explicites nullable.
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */
return new class extends Migration
{
    public function up(): void
    {
        // TEXT (hors page InnoDB) : ~60 VARCHAR(255) utf8mb4 dépasseraient la limite
        // de taille de ligne MySQL (65535) — erreur 1118 « Row size too large ».
        Schema::table('creatures', function (Blueprint $table): void {
            foreach (CreatureComposableColumns::all() as $column) {
                $context = CreatureComposableColumns::contextColumn($column);
                if (! Schema::hasColumn('creatures', $context)) {
                    $table->text($context)->nullable();
                }
            }
        });

        // Totaux explicites : null = « non défini, utiliser la composition ».
        // On passe aussi les totaux encore en VARCHAR vers TEXT pour libérer de la
        // place ligne (même contrainte 65535).
        foreach (CreatureComposableColumns::all() as $column) {
            if (! Schema::hasColumn('creatures', $column)) {
                continue;
            }
            DB::statement("ALTER TABLE `creatures` MODIFY `{$column}` TEXT NULL DEFAULT NULL");
        }
    }

    public function down(): void
    {
        $alreadyText = [
            'res_fixe_neutre', 'res_fixe_terre', 'res_fixe_feu', 'res_fixe_air', 'res_fixe_eau',
        ];
        foreach (CreatureComposableColumns::all() as $column) {
            if (! Schema::hasColumn('creatures', $column)) {
                continue;
            }
            DB::table('creatures')->whereNull($column)->update([$column => '0']);
            if (in_array($column, $alreadyText, true)) {
                DB::statement("ALTER TABLE `creatures` MODIFY `{$column}` TEXT NOT NULL");
            } else {
                DB::statement("ALTER TABLE `creatures` MODIFY `{$column}` VARCHAR(255) NOT NULL DEFAULT '0'");
            }
        }

        Schema::table('creatures', function (Blueprint $table): void {
            $drop = [];
            foreach (CreatureComposableColumns::contextColumns() as $context) {
                if (Schema::hasColumn('creatures', $context)) {
                    $drop[] = $context;
                }
            }
            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
