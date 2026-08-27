<?php

declare(strict_types=1);

namespace App\Models\Type\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Flags communs des registres de types : visibilité catalogue et autorisation de scrap.
 *
 * @mixin Model
 *
 * @property bool $show_in_catalog
 * @property bool $allow_scrap
 *
 * @method static Builder<static> allowScrap()
 * @method static Builder<static> visibleInCatalog()
 * @method static Builder<static> allowed()
 */
trait HasTypeRegistryFlags
{
    public function initializeHasTypeRegistryFlags(): void
    {
        $this->mergeFillable(['show_in_catalog', 'allow_scrap']);
        $this->mergeCasts([
            'show_in_catalog' => 'boolean',
            'allow_scrap' => 'boolean',
        ]);
    }

    /**
     * Types autorisés au scrap / à la maj DofusDB.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     *
     * @example ItemType::query()->allowScrap()->pluck('dofusdb_type_id');
     */
    public function scopeAllowScrap(Builder $query): Builder
    {
        return $query->where('allow_scrap', true);
    }

    /**
     * Types affichés par défaut dans les tableaux / catalogues.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     *
     * @example MonsterRace::query()->visibleInCatalog()->orderBy('name')->get();
     */
    public function scopeVisibleInCatalog(Builder $query): Builder
    {
        return $query->where('show_in_catalog', true);
    }

    /**
     * Alias historique : whitelist scrap (`decision=allowed`).
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeAllowed(Builder $query): Builder
    {
        return $this->scopeAllowScrap($query);
    }

    protected static function bootHasTypeRegistryFlags(): void
    {
        static::saving(function (Model $model): void {
            static::syncDecisionWithAllowScrap($model);

            if ($model->isDirty('allow_scrap')) {
                static::forgetAllowedTypeIdsCache();
            }
        });
    }

    /**
     * Garde `decision` aligné sur `allow_scrap` (colonne historique item/resource/consumable).
     */
    protected static function syncDecisionWithAllowScrap(Model $model): void
    {
        if (! in_array('decision', $model->getFillable(), true)) {
            return;
        }

        if ($model->isDirty('allow_scrap')) {
            $allow = (bool) $model->getAttribute('allow_scrap');
            if ($allow) {
                $model->setAttribute('decision', 'allowed');
            } elseif ((string) $model->getAttribute('decision') === 'allowed') {
                $model->setAttribute('decision', 'blocked');
            }

            return;
        }

        if ($model->isDirty('decision')) {
            $model->setAttribute('allow_scrap', (string) $model->getAttribute('decision') === 'allowed');
        }
    }

    protected static function forgetAllowedTypeIdsCache(): void
    {
        foreach (['resource', 'consumable', 'equipment'] as $entity) {
            Cache::forget("scrapping_allowed_type_ids_{$entity}");
        }
    }
}
