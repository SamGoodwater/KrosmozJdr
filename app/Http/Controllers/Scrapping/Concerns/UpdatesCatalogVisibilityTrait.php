<?php

declare(strict_types=1);

namespace App\Http\Controllers\Scrapping\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mise à jour unitaire du flag `show_in_catalog` (visibilités catalogues, pas le scrap).
 *
 * @internal
 */
trait UpdatesCatalogVisibilityTrait
{
    /**
     * @example PATCH /api/scrapping/item-types/{id}/catalog { "show_in_catalog": true }
     */
    protected function updateShowInCatalog(Request $request, Model $model): JsonResponse
    {
        $this->authorize('update', $model);

        $request->validate([
            'show_in_catalog' => ['required', 'boolean'],
        ]);

        $model->setAttribute('show_in_catalog', $request->boolean('show_in_catalog'));
        $model->save();

        return response()->json([
            'success' => true,
            'show_in_catalog' => (bool) $model->getAttribute('show_in_catalog'),
        ]);
    }
}
