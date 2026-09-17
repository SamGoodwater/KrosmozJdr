<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Rules\GameDownloadCatalog;
use Illuminate\Http\JsonResponse;

/**
 * Catalogue public des fichiers téléchargeables (page Ressources).
 */
class GameDownloadCatalogController extends Controller
{
    public function index(GameDownloadCatalog $catalog): JsonResponse
    {
        $items = $catalog->list();
        $groups = [];
        foreach ($items as $item) {
            $groupKey = $item['group'];
            if (! isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'key' => $groupKey,
                    'label' => $item['group_label'],
                    'items' => [],
                ];
            }
            $groups[$groupKey]['items'][] = $item;
        }

        return response()->json([
            'groups' => array_values($groups),
            'generated' => $catalog->generatedStatus(),
        ]);
    }
}
