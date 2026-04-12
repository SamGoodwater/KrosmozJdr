<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use Illuminate\Http\JsonResponse;

/**
 * Endpoint public pour la lecture des normes (chartes) d'une caractéristique.
 * Utilisé par le template de section CMS « characteristic_norms ».
 */
class CharacteristicNormsController extends Controller
{
    private const POWER_LEVELS = [
        'very_weak' => 'Très faible',
        'weak' => 'Faible',
        'neutral' => 'Neutre',
        'strong' => 'Fort',
        'very_strong' => 'Très fort',
    ];

    public function show(string $key, string $entity = '*'): JsonResponse
    {
        $characteristic = Characteristic::where('key', $key)->first();
        if ($characteristic === null) {
            return response()->json(['error' => 'Caractéristique introuvable.'], 404);
        }

        $effective = $characteristic->effectiveCharacteristic();
        $row = $this->findGroupRow($characteristic->id, $entity);

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
            ],
            'power_levels' => self::POWER_LEVELS,
            'max_level' => 20,
            'available_characteristics' => $availableCharacteristics,
        ]);
    }

    private function findGroupRow(int $characteristicId, string $entity): CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null
    {
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
