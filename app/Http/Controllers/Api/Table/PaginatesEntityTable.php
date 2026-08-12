<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Pagination optionnelle des endpoints Table v2.
 *
 * @description
 * - Sans `page` : comportement legacy (plafond `limit`, pas de meta pagination).
 * - Avec `page` : count + offset, meta `{ total, perPage, currentPage, lastPage }`.
 *
 * @example
 * $page = $this->paginateEntityTable($query, $request);
 * $rows = $page['rows'];
 * $limit = $page['limit'];
 */
trait PaginatesEntityTable
{
    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return array{
     *     rows: Collection<int, \Illuminate\Database\Eloquent\Model>,
     *     limit: int,
     *     page: int,
     *     pagination: array{total: int, perPage: int, currentPage: int, lastPage: int}|null
     * }
     */
    protected function paginateEntityTable(Builder $query, Request $request, int $defaultLimitWithoutPage = 5000): array
    {
        $hasPage = $request->has('page');
        $limit = (int) $request->integer('limit', $hasPage ? 25 : $defaultLimitWithoutPage);
        $limit = max(1, min($limit, 20000));
        $page = max(1, (int) $request->integer('page', 1));

        if (! $hasPage) {
            return [
                'rows' => $query->limit($limit)->get(),
                'limit' => $limit,
                'page' => $page,
                'pagination' => null,
            ];
        }

        $total = (int) $query->count();
        $lastPage = (int) max(1, (int) ceil($total / $limit));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $rows = $query->skip(($page - 1) * $limit)->limit($limit)->get();

        return [
            'rows' => $rows,
            'limit' => $limit,
            'page' => $page,
            'pagination' => [
                'total' => $total,
                'perPage' => $limit,
                'currentPage' => $page,
                'lastPage' => $lastPage,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $meta
     * @param  array{total: int, perPage: int, currentPage: int, lastPage: int}|null  $pagination
     * @return array<string, mixed>
     */
    protected function withEntityTablePaginationMeta(array $meta, ?array $pagination, int $limit, int $page): array
    {
        $meta['query'] = array_merge($meta['query'] ?? [], [
            'limit' => $limit,
            'page' => $page,
        ]);

        if ($pagination !== null) {
            $meta['pagination'] = $pagination;
        }

        return $meta;
    }
}
