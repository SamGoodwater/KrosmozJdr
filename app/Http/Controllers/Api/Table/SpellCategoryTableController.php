<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SpellCategoryTableController
 *
 * Endpoint "Table v2" pour les catégories de sorts (liste statique, pas de modèle).
 * Retourne les 4 catégories utilisées par le modèle Spell (0=Inconnu, 1=Offensif, 2=Défensif, 3=Utilitaire).
 * Compatible moteur de recherche : format=entities, search (filtrage côté réponse), sort.
 */
class SpellCategoryTableController extends Controller
{
    /** Catégories de sorts (alignées avec SpellTableController filterOptions). */
    private const CATEGORIES = [
        ['id' => 0, 'name' => 'Inconnu'],
        ['id' => 1, 'name' => 'Offensif'],
        ['id' => 2, 'name' => 'Défensif'],
        ['id' => 3, 'name' => 'Utilitaire'],
    ];

    public function index(Request $request): JsonResponse
    {
        // Pas de policy dédiée : les catégories sont des constantes métier, accessibles à tous les utilisateurs authentifiés.
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $search = $request->filled('search') ? (string) $request->get('search') : '';
        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'asc');
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'asc';
        }

        $rows = collect(self::CATEGORIES);

        if ($search !== '') {
            $searchLower = mb_strtolower($search);
            $rows = $rows->filter(fn (array $c) => str_contains(mb_strtolower((string) $c['name']), $searchLower));
        }

        $allowedSort = ['id', 'name'];
        if ($sort === 'name') {
            $rows = $order === 'desc'
                ? $rows->sortByDesc('name')
                : $rows->sortBy('name');
        } elseif ($sort === 'id' || ! in_array($sort, $allowedSort, true)) {
            $rows = $order === 'desc'
                ? $rows->sortByDesc('id')
                : $rows->sortBy('id');
        }

        $rows = $rows->values()->take($limit)->all();

        $filterOptions = [
            'id' => collect(self::CATEGORIES)->map(fn (array $c) => ['value' => (string) $c['id'], 'label' => $c['name']])->values()->all(),
        ];

        if ($format === 'entities') {
            $entities = array_map(fn (array $c) => [
                'id' => $c['id'],
                'name' => $c['name'],
            ], $rows);

            return response()->json([
                'meta' => [
                    'entityType' => 'spell-categories',
                    'query' => ['search' => $search, 'filters' => [], 'sort' => $sort, 'order' => $order, 'limit' => $limit],
                    'capabilities' => [],
                    'filterOptions' => $filterOptions,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = array_map(function (array $c) {
            return [
                'id' => $c['id'],
                'cells' => [
                    'id' => ['type' => 'text', 'value' => (string) $c['id'], 'params' => ['sortValue' => $c['id']]],
                    'name' => ['type' => 'text', 'value' => $c['name'], 'params' => ['searchValue' => $c['name'], 'sortValue' => $c['name']]],
                ],
                'rowParams' => ['entity' => ['id' => $c['id'], 'name' => $c['name']]],
            ];
        }, $rows);

        return response()->json([
            'meta' => [
                'entityType' => 'spell-categories',
                'query' => ['search' => $search, 'filters' => [], 'sort' => $sort, 'order' => $order, 'limit' => $limit],
                'filterOptions' => $filterOptions,
            ],
            'rows' => $tableRows,
        ]);
    }
}
