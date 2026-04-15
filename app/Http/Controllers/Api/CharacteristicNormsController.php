<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint public pour la lecture des normes (chartes) d'une caractéristique.
 * Utilisé par le template de section CMS « characteristic_norms ».
 */
class CharacteristicNormsController extends Controller
{
    /** @var list<string> */
    private const GROUPS = ['creature', 'object', 'spell'];

    private const POWER_LEVELS = [
        'very_weak' => 'Très faible',
        'weak' => 'Faible',
        'neutral' => 'Neutre',
        'strong' => 'Fort',
        'very_strong' => 'Très fort',
    ];

    public function show(Request $request, string $key, string $entity = '*'): JsonResponse
    {
        $characteristic = Characteristic::where('key', $key)->first();
        if ($characteristic === null) {
            return response()->json(['error' => 'Caractéristique introuvable.'], 404);
        }

        $group = $request->query('group');
        if (is_string($group) && $group !== '' && ! in_array($group, self::GROUPS, true)) {
            return response()->json(['error' => 'Groupe invalide. Utiliser creature, object ou spell.'], 422);
        }

        $effective = $characteristic->effectiveCharacteristic();
        $row = $this->findGroupRow($characteristic->id, $entity, is_string($group) ? $group : null);

        if ($row === null || $row->norms_grid === null) {
            return response()->json([
                'characteristic' => $this->formatCharacteristic($effective),
                'norms' => null,
                'power_levels' => self::POWER_LEVELS,
                'max_level' => 20,
            ]);
        }

        $conditionKeys = collect($row->norms_conditions ?? [])
            ->pluck('characteristic_key')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $availableCharacteristics = [];
        if ($conditionKeys !== []) {
            $availableCharacteristics = Characteristic::whereIn('key', $conditionKeys)
                ->get()
                ->map(fn (Characteristic $c) => [
                    'key' => $c->key,
                    'name' => $c->effectiveCharacteristic()->name ?? $c->key,
                ])
                ->keyBy('key')
                ->all();
        }

        return response()->json([
            'characteristic' => $this->formatCharacteristic($effective),
            'norms' => [
                'grid' => $row->norms_grid,
                'conditions' => $row->norms_conditions ?? [],
                'description' => $row->norms_description,
                'limits' => [
                    'min' => $row->min,
                    'max' => $row->max,
                ],
            ],
            'power_levels' => self::POWER_LEVELS,
            'max_level' => 20,
            'available_characteristics' => $availableCharacteristics,
        ]);
    }

    private function findGroupRow(
        int $characteristicId,
        string $entity,
        ?string $group
    ): CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null {
        if ($group !== null && $group !== '') {
            return $this->findRowByExplicitGroup($characteristicId, $entity, $group);
        }

        $row = CharacteristicCreature::where('characteristic_id', $characteristicId)
            ->where('entity', $entity)->first();
        if ($row !== null) {
            return $row;
        }

        $row = CharacteristicObject::where('characteristic_id', $characteristicId)
            ->where('entity', $entity)->first();
        if ($row !== null) {
            return $row;
        }

        return CharacteristicSpell::where('characteristic_id', $characteristicId)
            ->where('entity', $entity)->first();
    }

    private function findRowByExplicitGroup(
        int $characteristicId,
        string $entity,
        string $group
    ): CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null {
        return match ($group) {
            'creature' => CharacteristicCreature::where('characteristic_id', $characteristicId)
                ->where('entity', $entity)
                ->first(),
            'object' => CharacteristicObject::where('characteristic_id', $characteristicId)
                ->where('entity', $entity)
                ->first(),
            'spell' => CharacteristicSpell::where('characteristic_id', $characteristicId)
                ->where('entity', $entity)
                ->first(),
            default => null,
        };
    }

    /**
     * @return array{key: string, name: string, icon: string|null, color: string|null}
     */
    private function formatCharacteristic(Characteristic $c): array
    {
        return [
            'key' => $c->key,
            'name' => $c->name,
            'icon' => $c->icon,
            'color' => $c->color,
        ];
    }
}
