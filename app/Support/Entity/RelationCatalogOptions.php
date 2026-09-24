<?php

declare(strict_types=1);

namespace App\Support\Entity;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * DTOs légers pour catalogues de relations en édition Inertia.
 *
 * Évite {@see \Illuminate\Http\Resources\Json\JsonResource::collection} (can[],
 * relations, payloads massifs) : id + libellés suffisent pour seed / affichage,
 * la recherche complète passant par {@code api.tables.*} / EntityPicker.
 */
final class RelationCatalogOptions
{
    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  list<string>  $columns
     * @return list<array<string, mixed>>
     */
    public static function rows(Builder $query, array $columns, int $limit = 200): array
    {
        /** @var Collection<int, \Illuminate\Database\Eloquent\Model> $models */
        $models = $query->limit($limit)->get($columns);

        return $models
            ->map(static function ($model) use ($columns): array {
                $row = [];
                foreach ($columns as $column) {
                    $row[$column] = $model->getAttribute($column);
                }

                return $row;
            })
            ->values()
            ->all();
    }
}
