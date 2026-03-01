<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copie spell.area (entier) vers effect.area (notation string) pour les effets liés aux sorts,
 * puis supprime la colonne area de la table spells.
 *
 * Mapping ancien area (int) → notation zone : 0 => point, 1 => line-1x1, 2 => cross-1, 3 => circle-1 ;
 * autres valeurs => shape-{id} (à renseigner manuellement ensuite).
 */
return new class extends Migration
{
    private const INT_TO_AREA_NOTATION = [
        0 => 'point',
        1 => 'line-1x1',
        2 => 'cross-1',
        3 => 'circle-1',
    ];

    public function up(): void
    {
        $this->copySpellAreaToEffects();
        Schema::table('spells', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            $table->integer('area')->default(0)->after('effect');
        });
        $this->copyEffectAreaBackToSpells();
    }

    private function copySpellAreaToEffects(): void
    {
        $usages = DB::table('effect_usages')
            ->where('entity_type', 'spell')
            ->select('effect_id', 'entity_id')
            ->get();

        $spellAreas = DB::table('spells')->pluck('area', 'id')->all();

        foreach ($usages as $usage) {
            $spellId = (int) $usage->entity_id;
            $areaInt = isset($spellAreas[$spellId]) ? (int) $spellAreas[$spellId] : 0;
            $notation = self::INT_TO_AREA_NOTATION[$areaInt] ?? 'shape-' . $areaInt;

            DB::table('effects')->where('id', $usage->effect_id)->whereNull('area')->update(['area' => $notation]);
        }
    }

    private function copyEffectAreaBackToSpells(): void
    {
        $reverseMap = array_flip(self::INT_TO_AREA_NOTATION);
        $firstEffectPerSpell = DB::table('effect_usages')
            ->where('entity_type', 'spell')
            ->join('effects', 'effect_usages.effect_id', '=', 'effects.id')
            ->select('effect_usages.entity_id as spell_id', 'effects.area')
            ->orderBy('effect_usages.effect_id')
            ->get()
            ->unique('spell_id');

        foreach ($firstEffectPerSpell as $usage) {
            $spellId = (int) $usage->spell_id;
            $area = $usage->area;
            $intVal = isset($reverseMap[$area]) ? $reverseMap[$area] : 0;
            if (is_string($area) && preg_match('/^shape-(\d+)$/', $area, $m)) {
                $intVal = (int) $m[1];
            }

            DB::table('spells')->where('id', $spellId)->update(['area' => $intVal]);
        }
    }
};
