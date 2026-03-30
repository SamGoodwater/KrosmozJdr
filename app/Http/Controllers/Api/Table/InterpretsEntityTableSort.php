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
                $field = $item['field'] ?? $item['column'] ?? null;
                $dir = strtolower((string) ($item['dir'] ?? $item['order'] ?? 'asc'));
                if (! $field || ! in_array($field, $allowedSort, true)) {
                    continue;
                }
                if (! in_array($dir, ['asc', 'desc'], true)) {
                    $dir = 'asc';
                }
                $query->orderBy((string) $field, $dir);
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
        if (in_array($sort, $allowedSort, true)) {
            $query->orderBy($sort, $order);
        } else {
            $query->latest();
        }
    }
}
