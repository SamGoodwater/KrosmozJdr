<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Tri tableau API : format legacy `sort` + `order`, ou multi `sort[i][field]` + `sort[i][dir]`.
 *
 * @see resources/js/Composables/table/useTableServerParams.js (buildFetchUrl)
 */
trait InterpretsEntityTableSort
{
    /**
     * Alias colonne UI → colonne SQL (ids de colonnes TanStack ≠ noms de colonnes).
     *
     * @return array<string, string>
     */
    protected function entityTableSortAliases(): array
    {
        return [
            'item_type' => 'item_type_id',
            'resource_type' => 'resource_type_id',
            'consumable_type' => 'consumable_type_id',
            'monster_race' => 'monster_race_id',
            'price' => 'price_custom',
        ];
    }

    /**
     * @param  array<int, string>  $allowedSort
     */
    protected function resolveEntityTableSortField(string $field, array $allowedSort): ?string
    {
        if ($field === '') {
            return null;
        }

        $aliases = $this->entityTableSortAliases();
        $candidate = $aliases[$field] ?? $field;
        if (in_array($candidate, $allowedSort, true)) {
            return $candidate;
        }
        if (in_array($field, $allowedSort, true)) {
            return $field;
        }

        return null;
    }

    /**
     * @param  array<int, string>  $allowedSort
     */
    protected function applyEntityTableSort(Builder $query, Request $request, array $allowedSort, string $defaultSort = 'id', string $defaultOrder = 'desc'): void
    {
        $sorts = $request->input('sorts');
        if (is_array($sorts) && $sorts !== []) {
            $applied = false;
            foreach ($sorts as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $field = (string) ($item['field'] ?? $item['column'] ?? '');
                $resolved = $this->resolveEntityTableSortField($field, $allowedSort);
                $dir = strtolower((string) ($item['dir'] ?? $item['order'] ?? 'asc'));
                if ($resolved === null) {
                    continue;
                }
                if (! in_array($dir, ['asc', 'desc'], true)) {
                    $dir = 'asc';
                }
                $query->orderBy($resolved, $dir);
                $applied = true;
            }
            if ($applied) {
                return;
            }
        }

        $sort = (string) $request->get('sort', $defaultSort);
        $order = (string) $request->get('order', $defaultOrder);
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = $defaultOrder;
        }
        $resolved = $this->resolveEntityTableSortField($sort, $allowedSort);
        if ($resolved !== null) {
            $query->orderBy($resolved, $order);
        } else {
            $query->latest();
        }
    }
}
