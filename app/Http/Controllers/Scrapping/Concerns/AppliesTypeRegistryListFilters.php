<?php

declare(strict_types=1);

namespace App\Http\Controllers\Scrapping\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Filtres d’index des registres de types (`allow_scrap`, `show_in_catalog`).
 *
 * @internal
 */
trait AppliesTypeRegistryListFilters
{
    /**
     * @param  Builder<Model>  $query
     */
    protected function applyTypeRegistryListFilters(Builder $query, Request $request): void
    {
        if ($request->query->has('allow_scrap')) {
            $query->where('allow_scrap', $request->boolean('allow_scrap'));
        }
        if ($request->query->has('show_in_catalog')) {
            $query->where('show_in_catalog', $request->boolean('show_in_catalog'));
        }
    }
}
