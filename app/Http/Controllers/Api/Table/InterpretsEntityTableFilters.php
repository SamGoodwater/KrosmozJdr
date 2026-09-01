<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Table;

use App\Services\Characteristic\Formula\FormulaMinInteger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filtres tableau API : listes (CSV ou `filters[k][]`), égalité / whereIn, whitelist d’ids.
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

        if ($this->normalizeRangeBounds($raw) !== null) {
            return true;
        }

        return $this->normalizeFilterList($raw) !== [];
    }

    /**
     * @return array{min: int, max: int}|null
     */
    protected function normalizeRangeBounds(mixed $raw): ?array
    {
        if (! is_array($raw) || array_is_list($raw)) {
            return null;
        }
        if (! array_key_exists('min', $raw) && ! array_key_exists('max', $raw)) {
            return null;
        }

        $min = isset($raw['min']) && $raw['min'] !== '' && $raw['min'] !== null
            ? (int) $raw['min']
            : null;
        $max = isset($raw['max']) && $raw['max'] !== '' && $raw['max'] !== null
            ? (int) $raw['max']
            : null;
        if ($min === null && $max === null) {
            return null;
        }
        $lo = $min ?? $max;
        $hi = $max ?? $min;
        if ($lo === null || $hi === null) {
            return null;
        }

        return ['min' => min($lo, $hi), 'max' => max($lo, $hi)];
    }

    /**
     * Filtre une colonne texte/formule dont on compare l’entier minimal.
     *
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    protected function applyIntegerRangeFilter(Builder $query, string $column, mixed $raw): void
    {
        $range = $this->normalizeRangeBounds($raw);
        if ($range === null) {
            return;
        }

        $matching = $this->matchingFormulaMinValues(
            $query->clone()->reorder()->select($column)->distinct()->pluck($column)->all(),
            $range['min'],
            $range['max']
        );
        if ($matching === []) {
            $query->whereRaw('0 = 1');

            return;
        }

        $query->whereIn($column, $matching);
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    protected function applyRelationIntegerRangeFilter(
        Builder $query,
        string $relation,
        string $column,
        mixed $raw
    ): void {
        $range = $this->normalizeRangeBounds($raw);
        if ($range === null) {
            return;
        }

        $related = $query->getModel()->{$relation}()->getRelated();
        $matching = $this->matchingFormulaMinValues(
            $related->newQuery()->select($column)->distinct()->pluck($column)->all(),
            $range['min'],
            $range['max']
        );
        if ($matching === []) {
            $query->whereRaw('0 = 1');

            return;
        }

        $query->whereHas($relation, function (Builder $q) use ($column, $matching) {
            $q->whereIn($column, $matching);
        });
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return array{min: int, max: int}
     */
    protected function integerColumnBounds(Builder $query, string $column, int $fallbackMin = 0, int $fallbackMax = 20): array
    {
        $values = $this->formulaMinsForValues(
            $query->clone()->reorder()->select($column)->distinct()->pluck($column)->all()
        );
        if ($values === []) {
            return ['min' => $fallbackMin, 'max' => $fallbackMax];
        }

        return ['min' => min($values), 'max' => max($values)];
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return array{min: int, max: int}
     */
    protected function relationIntegerColumnBounds(
        Builder $query,
        string $relation,
        string $column,
        int $fallbackMin = 0,
        int $fallbackMax = 20
    ): array {
        $related = $query->getModel()->{$relation}()->getRelated();

        return $this->integerColumnBounds($related->newQuery(), $column, $fallbackMin, $fallbackMax);
    }

    /**
     * @param  list<mixed>  $values
     * @return list<mixed>
     */
    private function matchingFormulaMinValues(array $values, int $min, int $max): array
    {
        $matching = [];
        foreach ($values as $value) {
            $int = $this->formulaMinInteger()->min($value === null ? null : (string) $value);
            if ($int === null) {
                continue;
            }
            if ($int >= $min && $int <= $max) {
                $matching[] = $value;
            }
        }

        return $matching;
    }

    /**
     * @param  list<mixed>  $values
     * @return list<int>
     */
    private function formulaMinsForValues(array $values): array
    {
        $out = [];
        foreach ($values as $value) {
            $int = $this->formulaMinInteger()->min($value === null ? null : (string) $value);
            if ($int !== null) {
                $out[] = $int;
            }
        }

        return $out;
    }

    private function formulaMinInteger(): FormulaMinInteger
    {
        return app(FormulaMinInteger::class);
    }

    /**
     * Applique un filtre d’égalité (une valeur) ou whereIn (plusieurs).
     *
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
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
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
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
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
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
