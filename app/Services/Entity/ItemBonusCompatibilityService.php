<?php

declare(strict_types=1);

namespace App\Services\Entity;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Support\Entity\KrosmozItemBonusDecoder;
use App\Support\Entity\KrosmozItemEffectValidator;

/**
 * Audit et correction des clés `effect` / bonus objets (compatibilité type + limites).
 */
final class ItemBonusCompatibilityService
{
    public function __construct(
        private readonly KrosmozItemBonusDecoder $decoder,
        private readonly KrosmozItemEffectValidator $validator,
    ) {}

    /**
     * @param  iterable<int, Item>  $items
     * @return list<array{item_id: int, name: string, state: string, source: string, issues: list<string>}>
     */
    public function audit(iterable $items): array
    {
        $rows = [];
        foreach ($items as $item) {
            if (! $item instanceof Item) {
                continue;
            }
            [$source, $map] = $this->resolveAuditMap($item);
            if ($map === []) {
                continue;
            }
            $issues = $this->validator->validateBonusMap(
                $this->intMap($map),
                $item->item_type_id !== null ? (int) $item->item_type_id : null
            );
            if ($issues === []) {
                continue;
            }
            $rows[] = [
                'item_id' => (int) $item->id,
                'name' => (string) $item->name,
                'state' => (string) $item->state,
                'source' => $source,
                'issues' => $issues,
            ];
        }

        return $rows;
    }

    /**
     * @return array{updated: int, skipped: int, reports: list<array{item_id: int, removed: list<string>}>}
     */
    public function repair(bool $apply): array
    {
        $updated = 0;
        $skipped = 0;
        $reports = [];

        $query = Item::query()->orderBy('id');
        foreach ($query->cursor() as $item) {
            if (! $this->isRepairableState((string) $item->state)) {
                $skipped++;

                continue;
            }

            $sanitized = $this->sanitizeFlatEffect($item);
            if ($sanitized === null) {
                $skipped++;

                continue;
            }

            [$newEffect, $removed] = $sanitized;
            if ($removed === []) {
                continue;
            }

            $reports[] = [
                'item_id' => (int) $item->id,
                'removed' => $removed,
                'new_effect' => $newEffect,
            ];

            if ($apply) {
                $item->effect = $newEffect;
                $item->save();
                $updated++;
            }
        }

        return ['updated' => $updated, 'skipped' => $skipped, 'reports' => $reports];
    }

    public function isRepairableState(string $state): bool
    {
        return in_array($state, [
            EntityState::Raw->value,
            EntityState::Draft->value,
            EntityState::Auto->value,
        ], true);
    }

    /**
     * @return array{0: string, 1: array<string, float>}|null
     */
    private function resolveAuditMap(Item $item): array
    {
        $flatEffect = $this->parseFlatJsonMap($item->effect);
        if ($flatEffect !== null && $flatEffect !== []) {
            return ['effect', $flatEffect];
        }

        $decoded = $this->decoder->decode($item->effect, null);
        if ($decoded !== []) {
            return ['effect', $decoded];
        }

        $flatBonus = $this->parseFlatJsonMap($item->bonus);
        if ($flatBonus !== null && $flatBonus !== []) {
            return ['bonus', $flatBonus];
        }

        $decodedBonus = $this->decoder->decode(null, $item->bonus);
        if ($decodedBonus !== []) {
            return ['bonus', $decodedBonus];
        }

        return ['', []];
    }

    /**
     * @return array{0: ?string, 1: list<string>}|null null si effect non réparable (non plat ou absent).
     */
    private function sanitizeFlatEffect(Item $item): ?array
    {
        $map = $this->parseFlatJsonMap($item->effect);
        if ($map === null) {
            return null;
        }

        $typeId = $item->item_type_id !== null ? (int) $item->item_type_id : null;
        $intMap = $this->intMap($map);
        $removed = [];
        $kept = [];
        foreach ($intMap as $key => $value) {
            $issues = $this->validator->validateBonusMap([$key => $value], $typeId);
            if ($issues === []) {
                $kept[$key] = $value;
            } else {
                $removed[] = $key;
            }
        }

        if ($removed === []) {
            return [$item->effect, []];
        }

        $newEffect = $kept === [] ? null : json_encode($kept, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        return [$newEffect, $removed];
    }

    /**
     * @return array<string, float>|null null si absent ou non objet plat
     */
    private function parseFlatJsonMap(?string $json): ?array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }
        try {
            $decoded = json_decode(trim($json), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
        if (! is_array($decoded) || array_is_list($decoded)) {
            return null;
        }
        $out = [];
        foreach ($decoded as $key => $raw) {
            if (! is_string($key) || $key === '' || is_numeric($key)) {
                return null;
            }
            if (is_array($raw)) {
                return null;
            }
            if (! is_numeric($raw)) {
                return null;
            }
            $out[$key] = (float) $raw;
        }

        return $out;
    }

    /**
     * @param  array<string, float>  $map
     * @return array<string, int>
     */
    private function intMap(array $map): array
    {
        $out = [];
        foreach ($map as $k => $v) {
            $out[(string) $k] = (int) round($v);
        }

        return $out;
    }
}
