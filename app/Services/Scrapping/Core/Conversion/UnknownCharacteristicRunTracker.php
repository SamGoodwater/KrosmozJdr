<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion;

/**
 * Agrège les IDs characteristic DofusDB non mappés par run_id.
 */
final class UnknownCharacteristicRunTracker
{
    /**
     * @var array<string, array<int, int>>
     */
    private static array $countsByRun = [];

    public static function reset(?string $runId): void
    {
        if (!is_string($runId) || $runId === '') {
            return;
        }
        self::$countsByRun[$runId] = [];
    }

    /**
     * @param array<int, int> $counts
     */
    public static function addCounts(?string $runId, array $counts): void
    {
        if (!is_string($runId) || $runId === '' || $counts === []) {
            return;
        }
        if (!isset(self::$countsByRun[$runId])) {
            self::$countsByRun[$runId] = [];
        }
        foreach ($counts as $id => $count) {
            $key = (int) $id;
            $val = (int) $count;
            if ($key <= 0 || $val <= 0) {
                continue;
            }
            self::$countsByRun[$runId][$key] = (self::$countsByRun[$runId][$key] ?? 0) + $val;
        }
    }

    /**
     * @return array{
     *   total_occurrences: int,
     *   distinct_ids: int,
     *   ids: array<int, int>,
     *   contains_id_38: bool
     * }|null
     */
    public static function summary(?string $runId): ?array
    {
        if (!is_string($runId) || $runId === '' || !isset(self::$countsByRun[$runId])) {
            return null;
        }
        $ids = self::$countsByRun[$runId];
        ksort($ids);
        $total = 0;
        foreach ($ids as $count) {
            $total += (int) $count;
        }

        return [
            'total_occurrences' => $total,
            'distinct_ids' => count($ids),
            'ids' => $ids,
            'contains_id_38' => isset($ids[38]),
        ];
    }
}

