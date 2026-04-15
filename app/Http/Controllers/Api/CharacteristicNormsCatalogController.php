<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Liste les caractéristiques d’un groupe ayant une grille de normes définie.
 * Sert au template de section « characteristic_norms_catalog » (accordéon public).
 */
class CharacteristicNormsCatalogController extends Controller
{
    /** @var list<string> */
    private const ALLOWED_GROUPS = ['creature', 'object', 'spell'];

    public function show(Request $request, string $group, string $entity = '*'): JsonResponse
    {
        if (! in_array($group, self::ALLOWED_GROUPS, true)) {
            return response()->json(['error' => 'Groupe invalide. Utiliser creature, object ou spell.'], 404);
        }

        $entity = $entity === '' ? '*' : $entity;

        $rows = $this->queryPivotRows($group, $entity);

        if ($rows->isEmpty() && $entity !== '*') {
            $rows = $this->queryPivotRows($group, '*');
        }

        $items = $rows
            ->filter(fn ($row) => $row->characteristic !== null && is_array($row->norms_grid))
            ->map(function ($row) {
                $effective = $row->characteristic->effectiveCharacteristic();

                return [
                    'key' => $effective->key,
                    'name' => $effective->name ?? $effective->key,
                ];
            })
            ->unique('key')
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $filterKeys = $this->parseKeysFilter($request->query('keys'));
        if ($filterKeys !== []) {
            $set = array_flip($filterKeys);
            $items = array_values(array_filter($items, fn (array $item) => isset($set[$item['key']])));
        }

        return response()->json([
            'group' => $group,
            'entity' => $entity,
            'items' => $items,
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, CharacteristicCreature|CharacteristicObject|CharacteristicSpell>
     */
    private function queryPivotRows(string $group, string $entity)
    {
        $query = match ($group) {
            'creature' => CharacteristicCreature::query(),
            'object' => CharacteristicObject::query(),
            'spell' => CharacteristicSpell::query(),
        };

        return $query
            ->where('entity', $entity)
            ->whereNotNull('norms_grid')
            ->with(['characteristic'])
            ->get();
    }

    /**
     * @return list<string>
     */
    private function parseKeysFilter(mixed $keysParam): array
    {
        if ($keysParam === null || $keysParam === '') {
            return [];
        }
        if (is_string($keysParam)) {
            $parts = preg_split('/[,\s]+/', $keysParam, -1, PREG_SPLIT_NO_EMPTY);

            return $parts === false ? [] : array_values(array_filter(array_map('trim', $parts)));
        }
        if (is_array($keysParam)) {
            return array_values(array_filter(array_map(static fn ($k) => is_string($k) ? trim($k) : '', $keysParam)));
        }

        return [];
    }
}
