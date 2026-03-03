<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Portée des sorts : deux valeurs min/max (ou formules) à la place d'une seule colonne po.
 *
 * - po_min / po_max (string) : valeur entière "0", "1" ou formule "[level]", "[level]*2", etc.
 * - 0 = peut se lancer sur soi-même ; 1-1 = cac (mêlée) ; 2-6 = plage.
 * - L'ancienne colonne po est supprimée ; le modèle expose un accesseur ->po pour l'affichage "min-max".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            $table->string('po_min', 64)->nullable()->after('level');
            $table->string('po_max', 64)->nullable()->after('po_min');
        });

        $this->backfillFromPo();

        Schema::table('spells', function (Blueprint $table) {
            $table->dropColumn('po');
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            $table->string('po')->default('1')->after('level');
        });

        DB::table('spells')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $min = $row->po_min ?? '1';
                $max = $row->po_max ?? $row->po_min ?? '1';
                $po = $min === $max ? $min : $min . '-' . $max;
                DB::table('spells')->where('id', $row->id)->update(['po' => $po]);
            }
        });

        Schema::table('spells', function (Blueprint $table) {
            $table->dropColumn(['po_min', 'po_max']);
        });
    }

    private function backfillFromPo(): void
    {
        $rows = DB::table('spells')->select('id', 'po')->get();
        foreach ($rows as $row) {
            $po = trim((string) ($row->po ?? '1'));
            $min = '1';
            $max = '1';
            if (str_contains($po, '-')) {
                $parts = explode('-', $po, 2);
                $min = trim($parts[0]) !== '' ? $parts[0] : '1';
                $max = trim($parts[1] ?? '') !== '' ? trim($parts[1]) : $min;
            } else {
                $min = $po !== '' ? $po : '1';
                $max = $min;
            }
            DB::table('spells')->where('id', $row->id)->update([
                'po_min' => $min,
                'po_max' => $max,
            ]);
        }
    }
};
