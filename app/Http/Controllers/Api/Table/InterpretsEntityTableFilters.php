<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Table;

use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Filtres tableau API : listes (CSV ou `filters[k][]`), plages `filters[k][min|max]`, égalité / whereIn, whitelist d’ids.
 *
 * @see resources/js/Composables/table/useTableServerParams.js
 */
trait InterpretsEntityTableFilters
{
    /**
     * Normalise une valeur de filtre multi (tableau, CSV, scalaire) en liste de chaînes non vides.
     *
     * @return list<string>
     */
    protected function normalizeFilterList(mixed $raw): array
    {
        if ($raw === null || $raw === '' || $raw === false) {
            return [];
        }

        if (is_array($raw)) {
            $values = $raw;
        } else {
            $values = explode(',', (string) $raw);
        }

        $out = [];
        foreach ($values as $value) {
            if ($value === null || $value === '' || $value === false) {
                continue;
            }
            $out[] = trim((string) $value);
        }

        return array_values(array_filter($out, fn (string $v) => $v !== ''));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function hasFilterValue(array $filters, string $key): bool
    {
        if (! array_key_exists($key, $filters)) {
            return false;
        }

        $raw = $filters[$key];
        if ($raw === null || $raw === '') {
            return false;
        }

        if ($this->isRangeFilterValue($raw)) {
            return $this->normalizeRangeBounds($raw) !== null;
        }

        return $this->normalizeFilterList($raw) !== [];
    }

    /**
     * Payload plage : tableau associatif avec `min` et/ou `max` (pas une liste `[]`).
     */
    protected function isRangeFilterValue(mixed $raw): bool
    {
        if (! is_array($raw) || $raw === []) {
            return false;
        }

        return array_key_exists('min', $raw) || array_key_exists('max', $raw);
    }

    /**
     * @return array{0: ?int, 1: ?int}|null
     */
    protected function normalizeRangeBounds(mixed $raw): ?array
    {
        if (! $this->isRangeFilterValue($raw)) {
            return null;
        }

        $min = $this->nullableInt($raw['min'] ?? null);
        $max = $this->nullableInt($raw['max'] ?? null);
        if ($min === null && $max === null) {
            return null;
        }
        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }

        return [$min, $max];
    }

    protected function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    /**
     * Filtre entier min/max (`CAST AS SIGNED`). Si le payload n’est pas une plage, égalité / whereIn.
     *
     * @param  Builder<Model>  $query
     */
    protected function applyIntegerRangeFilter(Builder $query, string $column, mixed $raw): void
    {
        $bounds = $this->normalizeRangeBounds($raw);
        if ($bounds === null) {
            $this->applyEqualityFilter($query, $column, $raw, 'string');

            return;
        }

        [$min, $max] = $bounds;
        $qualified = $query->getModel()->qualifyColumn($column);
        $query->whereNotNull($column)->where($column, '!=', '');
        if ($min !== null) {
            $query->whereRaw("CAST({$qualified} AS SIGNED) >= ?", [$min]);
        }
        if ($max !== null) {
            $query->whereRaw("CAST({$qualified} AS SIGNED) <= ?", [$max]);
        }
    }

    /**
     * Plage entière sur une colonne de relation (`whereHas`).
     *
     * @param  Builder<Model>  $query
     */
    protected function applyRelationIntegerRangeFilter(
        Builder $query,
        string $relation,
        string $column,
        mixed $raw,
        string $cast = 'string'
    ): void {
        $bounds = $this->normalizeRangeBounds($raw);
        if ($bounds === null) {
            $this->applyRelationEqualityFilter($query, $relation, $column, $raw, $cast);

            return;
        }

        [$min, $max] = $bounds;
        $query->whereHas($relation, function (Builder $q) use ($column, $min, $max) {
            $qualified = $q->getModel()->qualifyColumn($column);
            $q->whereNotNull($column)->where($column, '!=', '');
            if ($min !== null) {
                $q->whereRaw("CAST({$qualified} AS SIGNED) >= ?", [$min]);
            }
            if ($max !== null) {
                $q->whereRaw("CAST({$qualified} AS SIGNED) <= ?", [$max]);
            }
        });
    }

    /**
     * Chevauchement d’intervalle (ex. PO sort : `po_min` / `po_max`).
     *
     * @param  Builder<Model>  $query
     */
    protected function applyIntegerRangeOverlapFilter(
        Builder $query,
        string $minColumn,
        string $maxColumn,
        mixed $raw
    ): void {
        $bounds = $this->normalizeRangeBounds($raw);
        if ($bounds === null) {
            return;
        }

        [$min, $max] = $bounds;
        $minQualified = $query->getModel()->qualifyColumn($minColumn);
        $maxQualified = $query->getModel()->qualifyColumn($maxColumn);

        if ($max !== null) {
            $query->whereRaw("CAST({$minQualified} AS SIGNED) <= ?", [$max]);
        }
        if ($min !== null) {
            $query->whereRaw("CAST(COALESCE({$maxQualified}, {$minQualified}) AS SIGNED) >= ?", [$min]);
        }
    }

    /**
     * Bornes min/max d’une colonne entière (CAST), hors lignes vides.
     *
     * @param  Builder<Model>  $query
     * @return array{min: int, max: int}
     */
    protected function integerColumnBounds(
        Builder $query,
        string $column,
        int $fallbackMin = 0,
        int $fallbackMax = 20
    ): array {
        $qualified = $query->getModel()->qualifyColumn($column);
        $scoped = $query->clone()->whereNotNull($column)->where($column, '!=', '');
        $min = $scoped->clone()->min(DB::raw("CAST({$qualified} AS SIGNED)"));
        $max = $scoped->clone()->max(DB::raw("CAST({$qualified} AS SIGNED)"));
        $minN = is_numeric($min) ? (int) $min : $fallbackMin;
        $maxN = is_numeric($max) ? (int) $max : $fallbackMax;
        if ($minN > $maxN) {
            [$minN, $maxN] = [$maxN, $minN];
        }

        return ['min' => $minN, 'max' => $maxN];
    }

    /**
     * Bornes min/max d’un alias `withCount` (sous-requête SELECT, pas une colonne physique).
     *
     * @param  Builder<Model>  $query  Query déjà pourvue du `withCount` correspondant
     * @return array{min: int, max: int}
     */
    protected function withCountColumnBounds(
        Builder $query,
        string $countAlias,
        int $fallbackMin = 0,
        int $fallbackMax = 20
    ): array {
        $alias = $this->safeSqlAlias($countAlias, 'items_count');
        $min = $query->clone()->reorder()->orderBy($alias)->value($alias);
        $max = $query->clone()->reorder()->orderByDesc($alias)->value($alias);
        $minN = is_numeric($min) ? (int) $min : $fallbackMin;
        $maxN = is_numeric($max) ? (int) $max : $fallbackMax;
        if ($minN > $maxN) {
            [$minN, $maxN] = [$maxN, $minN];
        }
        if ($minN === $maxN) {
            $minN = min($minN, $fallbackMin);
            $maxN = max($maxN, $fallbackMax);
        }

        return ['min' => $minN, 'max' => $maxN];
    }

    /**
     * Filtre plage sur un alias `withCount` via HAVING.
     *
     * @param  Builder<Model>  $query
     */
    protected function applyHavingRangeFilter(Builder $query, string $countAlias, mixed $raw): void
    {
        $alias = $this->safeSqlAlias($countAlias, 'items_count');
        $bounds = $this->normalizeRangeBounds($raw);
        if ($bounds === null) {
            $casted = $this->castFilterList($raw, 'int');
            if ($casted === []) {
                return;
            }
            if (count($casted) === 1) {
                $query->having($alias, '=', $casted[0]);

                return;
            }
            $placeholders = implode(', ', array_fill(0, count($casted), '?'));
            $query->havingRaw("{$alias} IN ({$placeholders})", $casted);

            return;
        }

        [$min, $max] = $bounds;
        if ($min !== null) {
            $query->having($alias, '>=', $min);
        }
        if ($max !== null) {
            $query->having($alias, '<=', $max);
        }
    }

    /**
     * Identifiant SQL (alias withCount) : lettres, chiffres, underscore.
     */
    protected function safeSqlAlias(string $alias, string $fallback): string
    {
        return preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $alias) === 1 ? $alias : $fallback;
    }

    /**
     * Bornes d’une colonne sur les fiches liées (ex. `creatures.level` via `monsters.creature_id`).
     *
     * @param  Builder<Model>  $ownerQuery
     * @param  class-string<Model>  $relatedClass
     * @return array{min: int, max: int}
     */
    protected function relatedIntegerColumnBounds(
        Builder $ownerQuery,
        string $foreignKey,
        string $relatedClass,
        string $column,
        int $fallbackMin = 0,
        int $fallbackMax = 20
    ): array {
        /** @var Model $related */
        $related = new $relatedClass;
        $relatedQuery = $relatedClass::query()->whereIn(
            $related->getQualifiedKeyName(),
            $ownerQuery->clone()->select($ownerQuery->getModel()->qualifyColumn($foreignKey))
        );

        return $this->integerColumnBounds($relatedQuery, $column, $fallbackMin, $fallbackMax);
    }

    /**
     * Entier min d’une formule au niveau 1 (bornes / tests). CAST SQL reste le filtre catalogue.
     */
    protected function formulaMinInteger(mixed $raw): ?int
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (is_numeric($raw)) {
            return (int) $raw;
        }

        $value = app(CharacteristicFormulaService::class)->evaluate(
            (string) $raw,
            ['level' => 1, 'niveau' => 1]
        );
        if ($value === null || ! is_finite($value)) {
            return null;
        }

        return (int) $value;
    }

    /**
     * Applique un filtre d’égalité (une valeur) ou whereIn (plusieurs).
     *
     * @param  Builder<Model>  $query
     * @param  'string'|'int'|'bool'  $cast
     */
    protected function applyEqualityFilter(Builder $query, string $column, mixed $raw, string $cast = 'string'): void
    {
        $casted = $this->castFilterList($raw, $cast);
        if ($casted === []) {
            return;
        }

        if (count($casted) === 1) {
            $query->where($column, $casted[0]);

            return;
        }

        $query->whereIn($column, $casted);
    }

    /**
     * Filtre via whereHas (colonne sur une relation).
     *
     * @param  Builder<Model>  $query
     * @param  'string'|'int'|'bool'  $cast
     */
    protected function applyRelationEqualityFilter(
        Builder $query,
        string $relation,
        string $column,
        mixed $raw,
        string $cast = 'string'
    ): void {
        $casted = $this->castFilterList($raw, $cast);
        if ($casted === []) {
            return;
        }

        $query->whereHas($relation, function (Builder $q) use ($column, $casted) {
            if (count($casted) === 1) {
                $q->where($column, $casted[0]);

                return;
            }
            $q->whereIn($column, $casted);
        });
    }

    /**
     * @param  Builder<Model>  $query
     */
    protected function applyEntityTableIdList(Builder $query, Request $request): void
    {
        $whitelist = $request->input('whitelist', $request->input('ids', []));
        $blacklist = $request->input('blacklist', $request->input('exclude', []));

        $whitelistIds = collect((array) $whitelist)
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->values()
            ->all();

        $blacklistIds = collect((array) $blacklist)
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->values()
            ->all();

        if ($whitelistIds !== []) {
            $query->whereIn($query->getModel()->getQualifiedKeyName(), $whitelistIds);
        }

        if ($blacklistIds !== []) {
            $query->whereNotIn($query->getModel()->getQualifiedKeyName(), $blacklistIds);
        }
    }

    /**
     * @param  'string'|'int'|'bool'  $cast
     * @return list<mixed>
     */
    protected function castFilterList(mixed $raw, string $cast): array
    {
        $values = $this->normalizeFilterList($raw);
        if ($values === []) {
            return [];
        }

        return array_map(fn (string $v) => $this->castFilterValue($v, $cast), $values);
    }

    /**
     * @param  'string'|'int'|'bool'  $cast
     */
    protected function castFilterValue(string $value, string $cast): mixed
    {
        return match ($cast) {
            'int' => (int) $value,
            'bool' => in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true) ? 1 : 0,
            default => $value,
        };
    }
}
