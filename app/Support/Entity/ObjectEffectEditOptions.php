<?php

declare(strict_types=1);

namespace App\Support\Entity;

use App\Http\Resources\ObjectEffectResource;
use App\Models\Characteristic;
use App\Models\Entity\Monster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Listes pour les sélecteurs d’effets d’objet (pages d’édition Inertia).
 */
final class ObjectEffectEditOptions
{
    /**
     * @return array{objectEffectCharacteristics: \Illuminate\Support\Collection<int, \App\Models\Characteristic>, objectEffectMonsters: \Illuminate\Support\Collection<int, object>}
     */
    public static function toArray(): array
    {
        $characteristics = Characteristic::query()
            ->where('group', 'object')
            ->orderBy('name')
            ->get(['id', 'key', 'name', 'short_name']);

        $monsters = Monster::query()
            ->join('creatures', 'creatures.id', '=', 'monsters.creature_id')
            ->orderBy('creatures.name')
            ->select(['monsters.id', 'creatures.name as name'])
            ->limit(2000)
            ->get();

        return [
            'objectEffectCharacteristics' => $characteristics,
            'objectEffectMonsters' => $monsters,
        ];
    }

    /**
     * Props Inertia : listes + effets déjà enregistrés pour l’entité (item, consommable, ressource).
     *
     * @return array<string, mixed>
     */
    public static function inertiaPropsFor(Model $parent, ?Request $request = null): array
    {
        $req = $request ?? request();

        return array_merge(self::toArray(), [
            'objectEffects' => ObjectEffectResource::collection(
                $parent->objectEffects()->with(['characteristic', 'monster.creature'])->orderBy('id')->get()
            )->toArray($req),
        ]);
    }
}
