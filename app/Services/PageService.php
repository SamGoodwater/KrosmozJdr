<?php

namespace App\Services;

use App\Models\Entity\Breed;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Service pour la gestion des pages et sections.
 *
 * Centralise la logique métier liée aux pages :
 * - Récupération des pages du menu
 * - Construction de l'arborescence du menu
 * - Vérification des permissions de visualisation
 * - Récupération des sections affichables
 *
 * @example
 * $menuPages = PageService::getMenuPages($user);
 * $menuTree = PageService::buildMenuTree($menuPages);
 */
class PageService
{
    /**
     * Durée du cache pour les pages du menu (en secondes).
     */
    private const CACHE_TTL = 3600; // 1 heure

    /**
     * Récupère les pages à afficher dans le menu.
     *
     * Filtre les pages selon :
     * - État : publiées uniquement
     * - Menu : in_menu = true
     * - Visibilité : selon le rôle de l'utilisateur
     * - Ordre : triées par menu_order
     *
     * @param  User|null  $user  Utilisateur connecté (null pour invité)
     * @return Collection<Page> Collection de pages
     */
    public static function getMenuPages(?User $user = null): Collection
    {
        $cacheKey = 'menu_pages_'.($user?->id ?? 'guest');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
            return Page::forMenu($user)
                ->with(['parent', 'children'])
                ->get();
        });
    }

    /**
     * Construit l'arborescence du menu à partir d'une collection de pages.
     *
     * Organise les pages en structure hiérarchique (parent/children) pour l'affichage
     * dans le menu de navigation. Les pages sont triées par `menu_order`.
     *
     * **Structure retournée :**
     * ```php
     * [
     *   ['id' => 1, 'title' => 'Page 1', 'url' => '/pages/page-1', 'children' => [...]],
     *   ['id' => 2, 'title' => 'Page 2', 'url' => '/pages/page-2', 'children' => []],
     * ]
     * ```
     *
     * @param  Collection<Page>  $pages  Collection de pages (doit contenir parent/children chargés)
     * @return array<int, array<string, mixed>> Arborescence du menu (pages racines avec enfants imbriqués)
     *
     * @example
     * $pages = PageService::getMenuPages($user);
     * $menuTree = PageService::buildMenuTree($pages);
     * // Utilisé pour afficher le menu hiérarchique dans le frontend
     */
    public static function buildMenuTree(Collection $pages): array
    {
        // Séparer les pages racines (sans parent) et les enfants
        $roots = $pages->filter(fn ($page) => $page->parent_id === null);
        $children = $pages->filter(fn ($page) => $page->parent_id !== null);
        $breedMenuIcons = self::prefetchBreedMenuIcons($pages);

        // Construire l'arborescence récursivement
        return $roots->map(function ($page) use ($children, $breedMenuIcons) {
            return self::buildMenuItem($page, $children, $breedMenuIcons);
        })->values()->toArray();
    }

    /**
     * Construit un item de menu avec ses enfants.
     *
     * @param  Page  $page  Page à transformer en item de menu
     * @param  Collection<Page>  $allChildren  Toutes les pages enfants disponibles
     * @return array<string, mixed> Item de menu avec structure
     */
    /**
     * @param  array<int, string|null>  $breedMenuIcons  id Breed => URL/chemin icône menu
     */
    private static function buildMenuItem(Page $page, Collection $allChildren, array $breedMenuIcons = []): array
    {
        $menuIcon = self::resolveMenuIconForPage($page, $breedMenuIcons);

        $item = [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'url' => route('pages.show', $page->slug, false),
            'order' => $page->menu_order,
            'menu_group' => $page->menu_group,
            'entity_key' => $page->entity_key,
            'icon' => $page->icon,
            'menu_icon' => $menuIcon,
            'page_css_classes' => $page->page_css_classes,
            'title_css_classes' => $page->title_css_classes,
            'menu_item_css_classes' => $page->menu_item_css_classes,
            'children' => [],
        ];

        // Trouver les enfants de cette page
        $pageChildren = $allChildren->filter(fn ($child) => $child->parent_id === $page->id);

        if ($pageChildren->isNotEmpty()) {
            // Construire récursivement les enfants
            $item['children'] = $pageChildren
                ->sortBy('menu_order')
                ->map(fn ($child) => self::buildMenuItem($child, $allChildren, $breedMenuIcons))
                ->values()
                ->toArray();
        }

        return $item;
    }

    /**
     * Précharge les icônes menu des classes liées (colonne icon + collection Spatie icons).
     *
     * @param  Collection<Page>  $pages
     * @return array<int, string|null>
     */
    private static function prefetchBreedMenuIcons(Collection $pages): array
    {
        $breedIds = $pages
            ->map(function (Page $page): int {
                $linked = $page->settings['linked_entity'] ?? null;
                if (! is_array($linked) || ($linked['type'] ?? '') !== 'breed') {
                    return 0;
                }

                return (int) ($linked['id'] ?? 0);
            })
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        if ($breedIds === []) {
            return [];
        }

        $map = [];
        Breed::query()
            ->whereIn('id', $breedIds)
            ->with(['media' => fn ($query) => $query->whereIn('collection_name', ['icons', 'images'])])
            ->get(['id', 'icon', 'image'])
            ->each(function (Breed $breed) use (&$map): void {
                $map[(int) $breed->id] = self::resolveBreedMenuIcon($breed);
            });

        return $map;
    }

    /**
     * @param  array<int, string|null>  $breedMenuIcons
     */
    private static function resolveMenuIconForPage(Page $page, array $breedMenuIcons): ?string
    {
        $linked = $page->settings['linked_entity'] ?? null;
        if (! is_array($linked)) {
            return null;
        }

        $type = (string) ($linked['type'] ?? '');
        if ($type !== 'breed') {
            return null;
        }

        $breedId = (int) ($linked['id'] ?? 0);
        if ($breedId < 1) {
            return null;
        }

        return $breedMenuIcons[$breedId] ?? null;
    }

    /**
     * Icône menu d'une classe : colonne icon, sinon image, puis médias Spatie icons / images.
     */
    public static function resolveBreedMenuIconForSync(Breed $breed): ?string
    {
        return self::resolveBreedMenuIcon($breed);
    }

    private static function resolveBreedMenuIcon(Breed $breed): ?string
    {
        foreach ([$breed->icon, $breed->image] as $columnValue) {
            $fromColumn = self::normalizeMenuIconUrl($columnValue);
            if ($fromColumn !== null) {
                return $fromColumn;
            }
        }

        foreach (['icons', 'images'] as $collection) {
            $media = $breed->getFirstMedia($collection);
            if ($media === null) {
                continue;
            }

            $url = $media->hasGeneratedConversion('thumb')
                ? $media->getUrl('thumb')
                : $media->getUrl();

            $normalized = self::normalizeMenuIconUrl($url);
            if ($normalized !== null) {
                return $normalized;
            }
        }

        return null;
    }

    /**
     * Exclut Font Awesome et chaînes vides ; renvoie un chemin relatif /storage/… si possible.
     */
    private static function normalizeMenuIconUrl(?string $icon): ?string
    {
        $sanitized = self::sanitizeMenuIcon($icon);
        if ($sanitized === null) {
            return null;
        }

        if (str_starts_with($sanitized, 'http://') || str_starts_with($sanitized, 'https://')) {
            $path = parse_url($sanitized, PHP_URL_PATH);
            if (is_string($path) && $path !== '') {
                return $path;
            }
        }

        return $sanitized;
    }

    /**
     * Exclut Font Awesome et chaînes vides des icônes menu image.
     */
    private static function sanitizeMenuIcon(?string $icon): ?string
    {
        if ($icon === null) {
            return null;
        }

        $trimmed = trim($icon);
        if ($trimmed === '' || str_starts_with($trimmed, 'fa-')) {
            return null;
        }

        return $trimmed;
    }

    /**
     * Vérifie si une page peut être vue par un utilisateur.
     *
     * @param  Page  $page  Page à vérifier
     * @param  User|null  $user  Utilisateur (null pour invité)
     * @return bool True si la page peut être vue
     */
    public static function canViewPage(Page $page, ?User $user = null): bool
    {
        return $page->canBeViewedBy($user);
    }

    /**
     * Récupère les sections affichables d'une page.
     *
     * Filtre les sections selon :
     * - État : publiées uniquement
     * - Visibilité : selon le rôle de l'utilisateur
     * - Ordre : triées par order
     *
     * @param  Page  $page  Page dont on veut les sections
     * @param  User|null  $user  Utilisateur connecté (null pour invité)
     * @return Collection<Section> Collection de sections
     */
    public static function getPublishedSections(Page $page, ?User $user = null): Collection
    {
        return SectionService::getDisplayableSections($page, $user);
    }

    /**
     * Invalide le cache des pages du menu.
     *
     * **Quand l'appeler :**
     * - Après création d'une page
     * - Après mise à jour d'une page (titre, slug, in_menu, parent_id, menu_order, etc.)
     * - Après suppression/restauration d'une page
     * - Après modification de la visibilité ou de l'état d'une page
     *
     * **Gestion du cache :**
     * - Le cache est séparé par utilisateur (chaque utilisateur a son propre cache)
     * - Si `$user` est null, invalide pour TOUS les utilisateurs (utilise `Cache::flush()`)
     * - Toujours invalide le cache des invités
     * - OPTIMISATION : Invalide aussi le cache de la liste des pages (select)
     *
     * @param  User|null  $user  Utilisateur spécifique (null pour tous les utilisateurs)
     *
     * @example
     * // Après modification d'une page
     * $page->update(['title' => 'Nouveau titre']);
     * PageService::clearMenuCache(); // Invalide pour tous
     *
     * // Après modification pour un utilisateur spécifique
     * PageService::clearMenuCache($user); // Invalide seulement pour cet utilisateur
     */
    public static function clearMenuCache(?User $user = null): void
    {
        if ($user) {
            Cache::forget('menu_pages_'.$user->id);
        } else {
            // Invalider pour tous les utilisateurs (pattern matching)
            // Note: Cache::flush() vide TOUT le cache, pas seulement les pages
            // Pour une meilleure performance, on pourrait utiliser un système de tags si disponible
            Cache::flush();
        }

        // Toujours invalider pour les invités
        Cache::forget('menu_pages_guest');

        // OPTIMISATION : Invalider le cache de la liste des pages (utilisé dans les selects)
        Cache::forget('pages_select_list');
    }

    /**
     * Récupère une page par son slug avec ses sections affichables.
     *
     * @param  string  $slug  Slug de la page
     * @param  User|null  $user  Utilisateur connecté (null pour invité)
     * @return Page|null Page trouvée ou null
     */
    public static function getPageBySlug(string $slug, ?User $user = null): ?Page
    {
        $page = Page::where('slug', $slug)->first();

        if (! $page || ! self::canViewPage($page, $user)) {
            return null;
        }

        // Charger les sections affichables via SectionService
        $sections = SectionService::getDisplayableSections($page, $user);
        $page->setRelation('sections', $sections);

        return $page;
    }

    /**
     * Vérifie si une page peut être affichée dans le menu.
     *
     * @param  Page  $page  Page à vérifier
     * @param  User|null  $user  Utilisateur connecté (null pour invité)
     * @return bool True si la page peut être dans le menu
     */
    public static function canBeInMenu(Page $page, ?User $user = null): bool
    {
        return $page->isPlayable()
            && $page->in_menu
            && $page->isReadableFor($user);
    }
}
