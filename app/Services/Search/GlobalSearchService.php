<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * Recherche globale unifiée (entités, pages, sections) avec respect des policies `view`.
 *
 * @example
 * app(GlobalSearchService::class)->search($request->user(), 'bouftou', [], [], 40);
 */
final class GlobalSearchService
{
    /** Ordre d'affichage des groupes de résultats et d'interrogation par défaut. */
    public const SEARCH_TYPE_ORDER = [
        'breeds',
        'specializations',
        'monsters',
        'spells',
        'capabilities',
        'creature-traits',
        'conditions',
        'items',
        'consumables',
        'resources',
        'panoplies',
        'campaigns',
        'scenarios',
        'npcs',
        'shops',
        'item-types',
        'consumable-types',
        'spell-types',
        'monster-races',
        'resource-types',
        'pages',
        'sections',
    ];

    /** @var list<string> */
    public const ALLOWED_TYPES = self::SEARCH_TYPE_ORDER;

    /** @var list<string> */
    public const ALLOWED_STATES = ['raw', 'draft', 'playable', 'archived'];

    /**
     * @param  list<string>  $types  Types à interroger (sous-ensemble de ALLOWED_TYPES)
     * @param  list<string>  $states  Filtre `state` ; vide = tous les états
     * @return array{results: list<array{id:int|string, entityType:string, group:string, title:string, subtitle:string, href:string, icon:string, iconUrl:string}>, hasMore: bool}
     */
    public function search(?User $user, string $query, array $types, array $states, int $limit): array
    {
        $term = trim($query);
        if (mb_strlen($term) < 2) {
            return ['results' => [], 'hasMore' => false];
        }

        if (mb_strlen($term) > 100) {
            $term = mb_substr($term, 0, 100);
        }

        $limit = max(1, min($limit, 80));
        $types = $this->normalizeTypes($types);
        $states = array_values(array_intersect(self::ALLOWED_STATES, $states));

        $perType = (int) max(3, ceil($limit / max(1, count($types))));
        $like = '%'.addcslashes($term, '%_\\').'%';

        $collected = [];

        foreach ($types as $type) {
            $group = $this->groupLabel($type);
            foreach ($this->fetchForType($type, $like, $states, $perType) as $model) {
                if (! Gate::forUser($user)->allows('view', $model)) {
                    continue;
                }
                $collected[] = $this->serializeHit($type, $group, $model);
            }
        }

        $sliced = array_slice($collected, 0, $limit);

        return [
            'results' => $sliced,
            'hasMore' => count($collected) > $limit,
        ];
    }

    /**
     * @param  list<string>  $types
     * @return list<string>
     */
    private function normalizeTypes(array $types): array
    {
        $allowed = array_flip(self::ALLOWED_TYPES);
        $filtered = [];
        foreach ($types as $type) {
            if (isset($allowed[$type])) {
                $filtered[$type] = true;
            }
        }

        if ($filtered === []) {
            return self::SEARCH_TYPE_ORDER;
        }

        $ordered = [];
        foreach (self::SEARCH_TYPE_ORDER as $type) {
            if (isset($filtered[$type])) {
                $ordered[] = $type;
            }
        }

        return $ordered;
    }

    /**
     * @param  list<string>  $states
     * @return list<Model>
     */
    private function fetchForType(string $type, string $like, array $states, int $perType): array
    {
        return match ($type) {
            'pages' => $this->queryPages($like, $states, $perType),
            'sections' => $this->querySections($like, $states, $perType),
            default => $this->queryRegistryEntity($type, $like, $states, $perType),
        };
    }

    /**
     * @param  list<string>  $states
     * @return list<Model>
     */
    private function queryRegistryEntity(string $type, string $like, array $states, int $perType): array
    {
        /** @var array<string, class-string<Model>> $registry */
        $registry = (array) Config::get('entity-permissions', []);
        $class = $registry[$type] ?? null;
        if ($class === null || ! class_exists($class)) {
            return [];
        }

        /** @var Builder<Model> $q */
        $q = $class::query();

        $probe = $q->getModel();

        if ($type === 'monsters') {
            $q->with(['creature', 'monsterRace']);
            $q->where(function (Builder $qq) use ($like): void {
                $qq->whereHas('creature', fn (Builder $c) => $c->where('name', 'like', $like))
                    ->orWhereHas('monsterRace', fn (Builder $r) => $r->where('name', 'like', $like));
            });
        } else {
            $cols = [];
            foreach (['name', 'title', 'slug', 'description', 'keyword', 'effect'] as $col) {
                if ($this->tableHasColumn($probe, $col)) {
                    $cols[] = $col;
                }
            }
            if ($cols === []) {
                return [];
            }
            $q->where(function (Builder $qq) use ($like, $cols): void {
                foreach ($cols as $i => $col) {
                    if ($i === 0) {
                        $qq->where((string) $col, 'like', $like);
                    } else {
                        $qq->orWhere((string) $col, 'like', $like);
                    }
                }
            });
        }

        if ($states !== [] && $this->tableHasColumn($probe, 'state')) {
            $q->whereIn('state', $states);
        }

        /** @var list<Model> $rows */
        $rows = $q->orderByDesc('id')->limit($perType)->get()->all();

        return $rows;
    }

    /**
     * @param  list<string>  $states
     * @return list<Page>
     */
    private function queryPages(string $like, array $states, int $perType): array
    {
        $q = Page::query()->where(function (Builder $qq) use ($like): void {
            $qq->where('title', 'like', $like)
                ->orWhere('slug', 'like', $like);
        });
        if ($states !== []) {
            $q->whereIn('state', $states);
        }

        return $q->orderByDesc('id')->limit($perType)->get()->all();
    }

    /** @var array<string, bool> */
    private static array $columnCache = [];

    private function tableHasColumn(Model $model, string $column): bool
    {
        $table = $model->getTable();
        $key = $table.'.'.$column;
        if (! array_key_exists($key, self::$columnCache)) {
            self::$columnCache[$key] = $model->getConnection()->getSchemaBuilder()->hasColumn($table, $column);
        }

        return self::$columnCache[$key];
    }

    /**
     * @param  list<string>  $states
     * @return list<Section>
     */
    private function querySections(string $like, array $states, int $perType): array
    {
        $q = Section::query()
            ->with(['page'])
            ->where(function (Builder $qq) use ($like): void {
                $qq->where('title', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('data', 'like', $like);
            });
        if ($states !== []) {
            $q->whereIn('state', $states);
        }

        return $q->orderByDesc('id')->limit($perType)->get()->all();
    }

    /**
     * @return array{id:int|string, entityType:string, group:string, title:string, subtitle:string, href:string, icon:string, iconUrl:string}
     */
    private function serializeHit(string $type, string $group, Model $model): array
    {
        $title = $this->inferTitle($type, $model);
        $subtitle = $this->inferSubtitle($model);
        $visual = $this->inferVisual($type, $model);

        return [
            'id' => $model->getKey(),
            'entityType' => $type,
            'group' => $group,
            'title' => $title,
            'subtitle' => $subtitle,
            'href' => $this->inferHref($type, $model),
            'icon' => $visual['icon'],
            'iconUrl' => $visual['iconUrl'],
        ];
    }

    /**
     * Visuel d’une ligne (URL image ou icône FA ; pas de requête média supplémentaire).
     *
     * @return array{icon: string, iconUrl: string}
     */
    private function inferVisual(string $type, Model $model): array
    {
        $iconUrl = $this->inferImageUrl($type, $model);
        if ($iconUrl !== '') {
            return ['icon' => '', 'iconUrl' => $iconUrl];
        }

        return ['icon' => $this->inferFaIcon($model), 'iconUrl' => ''];
    }

    private function inferImageUrl(string $type, Model $model): string
    {
        if ($type === 'monsters' && $model instanceof Monster) {
            $creature = $model->relationLoaded('creature') ? $model->creature : null;
            if ($creature instanceof Creature && $this->tableHasColumn($creature, 'image')) {
                $raw = (string) ($creature->image ?? '');

                return $this->looksLikeImageUrl($raw) ? $this->normalizeImageUrl($raw) : '';
            }
        }

        foreach (['image', 'icon'] as $attr) {
            if (! $this->tableHasColumn($model, $attr)) {
                continue;
            }
            $raw = (string) ($model->{$attr} ?? '');
            if (! $this->looksLikeImageUrl($raw)) {
                continue;
            }

            return $this->normalizeImageUrl($raw);
        }

        return '';
    }

    private function inferFaIcon(Model $model): string
    {
        if (! $this->tableHasColumn($model, 'icon')) {
            return '';
        }

        $icon = trim((string) ($model->icon ?? ''));
        if ($icon === '' || $this->looksLikeImageUrl($icon)) {
            return '';
        }

        return $icon;
    }

    private function looksLikeImageUrl(string $raw): bool
    {
        $value = trim($raw);
        if ($value === '') {
            return false;
        }
        if (str_starts_with($value, 'fa-') || str_contains($value, 'fa-solid') || str_contains($value, 'fa-regular')) {
            return false;
        }

        return str_starts_with($value, 'http://')
            || str_starts_with($value, 'https://')
            || str_starts_with($value, '/')
            || str_starts_with($value, 'storage/');
    }

    private function normalizeImageUrl(string $raw): string
    {
        $value = trim($raw);
        if ($value === '') {
            return '';
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/')) {
            return $value;
        }
        if (str_starts_with($value, 'storage/')) {
            return asset($value);
        }

        return asset('storage/'.$value);
    }

    private function inferTitle(string $type, Model $model): string
    {
        if ($type === 'monsters' && $model instanceof Monster) {
            $creatureName = $model->relationLoaded('creature') && $model->creature !== null
                ? trim((string) ($model->creature->name ?? ''))
                : '';
            if ($creatureName !== '') {
                return $creatureName;
            }

            $raceName = $model->relationLoaded('monsterRace') && $model->monsterRace !== null
                ? trim((string) ($model->monsterRace->name ?? ''))
                : '';
            if ($raceName !== '') {
                return $raceName;
            }

            return '#'.$model->getKey();
        }

        foreach (['name', 'title'] as $attr) {
            if (isset($model->{$attr}) && is_string($model->{$attr}) && trim($model->{$attr}) !== '') {
                return trim($model->{$attr});
            }
        }
        if (isset($model->slug) && is_string($model->slug)) {
            return $model->slug;
        }

        return '#'.$model->getKey();
    }

    private function inferSubtitle(Model $model): string
    {
        if ($model instanceof Section) {
            $bits = [];
            $page = $model->relationLoaded('page') ? $model->page : null;
            if ($page instanceof Page && is_string($page->title) && trim($page->title) !== '') {
                $bits[] = trim($page->title);
            }

            foreach (['description', 'keyword'] as $attr) {
                $v = $model->{$attr} ?? null;
                if (is_string($v) && trim($v) !== '') {
                    $bits[] = $this->excerpt($v);

                    break;
                }
            }

            return implode(' — ', array_filter($bits));
        }

        foreach (['description', 'keyword'] as $attr) {
            $v = $model->{$attr} ?? null;
            if (is_string($v) && trim($v) !== '') {
                return $this->excerpt($v);
            }
        }

        return '';
    }

    private function excerpt(string $htmlOrText): string
    {
        $plain = trim(strip_tags($htmlOrText));

        return Str::limit($plain, 140, '…');
    }

    private function inferHref(string $type, Model $model): string
    {
        if ($model instanceof Page) {
            return route('pages.show', ['page' => $model->slug]);
        }

        if ($model instanceof Section) {
            $page = $model->page;
            if ($page instanceof Page) {
                $base = route('pages.show', ['page' => $page->slug]);

                return $base.'#section-'.$model->getKey();
            }

            return '';
        }

        $id = $model->getKey();
        $slugOrId = $model->slug ?? $id;

        return match ($type) {
            'breeds' => route('entities.breeds.show', ['breed' => $slugOrId]),
            'creature-traits' => route('entities.creature-traits.show', ['creatureTrait' => $slugOrId]),
            'resource-types' => route('entities.resources.index').'?'.http_build_query(['resource_type_id' => $id]),
            'item-types' => route('entities.items.index').'?'.http_build_query(['item_type_id' => $id]),
            'consumable-types' => route('entities.consumables.index').'?'.http_build_query(['consumable_type_id' => $id]),
            'monster-races' => route('entities.monsters.index').'?'.http_build_query(['monster_race_id' => $id]),
            'spell-types' => route('entities.spells.index').'?'.http_build_query(['spell_type_id' => $id]),
            default => route('entities.'.$type.'.show', $slugOrId),
        };
    }

    private function groupLabel(string $type): string
    {
        return match ($type) {
            'conditions' => 'États',
            'campaigns' => 'Campagnes',
            'capabilities' => 'Capacités',
            'breeds' => 'Classes',
            'consumables' => 'Consommables',
            'creature-traits' => 'Traits de créature',
            'items' => 'Équipements',
            'monsters' => 'Monstres',
            'npcs' => 'PNJ',
            'panoplies' => 'Panoplies',
            'resources' => 'Ressources',
            'scenarios' => 'Scénarios',
            'shops' => 'Boutiques',
            'spells' => 'Sorts',
            'specializations' => 'Spécialisations',
            'resource-types' => 'Types de ressource',
            'item-types' => "Types d'équipement",
            'consumable-types' => 'Types de consommable',
            'spell-types' => 'Types de sort',
            'monster-races' => 'Races',
            'pages' => 'Pages',
            'sections' => 'Sections',
            default => $type,
        };
    }
}
