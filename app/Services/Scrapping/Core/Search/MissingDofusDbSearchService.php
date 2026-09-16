<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Search;

use App\Services\Scrapping\Core\Collect\CollectService;

/**
 * Recherche paginée des IDs DofusDB absents en base locale.
 *
 * Parcourt les pages DofusDB côté serveur (pas un filtre client) et
 * renvoie le total des manquants + la page demandée.
 */
final class MissingDofusDbSearchService
{
    public const DOFUSDB_PAGE_SIZE = 50;

    public const MAX_WALK_PAGES = 200;

    public function __construct(
        private CollectService $collectService,
        private SearchResultEnricher $enricher,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>  $collectOptions
     * @return array{items: list<array<string, mixed>>, meta: array<string, mixed>}
     */
    public function paginateMissing(
        string $uiEntity,
        string $collectEntity,
        array $filters,
        array $collectOptions,
        int $page,
        int $perPage,
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(200, $perPage));
        $localIds = $this->enricher->localDofusdbIdSet($uiEntity);
        $wantedOffset = ($page - 1) * $perPage;
        $pageItems = [];
        $missingTotal = 0;
        $skip = 0;
        $pagesFetched = 0;
        $dofusTotal = null;

        while ($pagesFetched < self::MAX_WALK_PAGES) {
            $chunkOptions = $collectOptions;
            $chunkOptions['start_skip'] = $skip;
            $chunkOptions['limit'] = self::DOFUSDB_PAGE_SIZE;
            $chunkOptions['max_items'] = self::DOFUSDB_PAGE_SIZE;
            $chunkOptions['max_pages'] = 1;
            unset($chunkOptions['offset']);

            $result = $this->collectService->fetchManyResult('dofusdb', $collectEntity, $filters, $chunkOptions);
            $items = $result['items'] ?? [];
            if ($dofusTotal === null && isset($result['meta']['total']) && is_int($result['meta']['total'])) {
                $dofusTotal = $result['meta']['total'];
            }
            if ($items === []) {
                break;
            }

            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $id = isset($item['id']) ? (string) (int) $item['id'] : '';
                if ($id === '' || $id === '0') {
                    continue;
                }
                if (isset($localIds[$id])) {
                    continue;
                }
                if ($missingTotal >= $wantedOffset && count($pageItems) < $perPage) {
                    $pageItems[] = $item;
                }
                $missingTotal++;
            }

            $skip += count($items);
            $pagesFetched++;
            if (count($items) < self::DOFUSDB_PAGE_SIZE) {
                break;
            }
            if (is_int($dofusTotal) && $skip >= $dofusTotal) {
                break;
            }
        }

        return [
            'items' => $pageItems,
            'meta' => [
                'total' => $missingTotal,
                'limit' => $perPage,
                'skip' => $wantedOffset,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => $missingTotal > 0 ? (int) ceil($missingTotal / $perPage) : 0,
                'only_missing' => true,
                'truncated' => $pagesFetched >= self::MAX_WALK_PAGES,
            ],
        ];
    }
}
