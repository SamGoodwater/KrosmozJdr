<?php

declare(strict_types=1);

namespace App\Support\Entity;

use App\Http\Resources\ObjectEffectResource;
use App\Models\Characteristic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Listes pour les sélecteurs d’effets d’objet (pages d’édition Inertia).
 *
 * Les monstres ne sont plus embarqués (EntityPicker / api.tables) — évite ~2k lignes.
 */
final class ObjectEffectEditOptions
{
    /**
     * @return array{objectEffectCharacteristics: Collection<int, Characteristic>, objectEffectMonsters: array<int, never>}
     */
    public static function toArray(): array
    {
        $characteristics = Characteristic::query()
            ->where('group', 'object')
            ->orderBy('name')
            ->get(['id', 'key', 'name', 'short_name']);

        return [
            'objectEffectCharacteristics' => $characteristics,
            'objectEffectMonsters' => [],
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
