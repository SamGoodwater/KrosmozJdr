<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Enums\PageState;
use App\Enums\Visibility;
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
     * @param User|null $user Utilisateur connecté (null pour invité)
     * @return Collection<Page> Collection de pages
     */
    public static function getMenuPages(?User $user = null): Collection
    {
        $cacheKey = 'menu_pages_' . ($user?->id ?? 'guest');
        
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
     * @param Collection<Page> $pages Collection de pages (doit contenir parent/children chargés)
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
        $roots = $pages->filter(fn($page) => $page->parent_id === null);
        $children = $pages->filter(fn($page) => $page->parent_id !== null);

        // Construire l'arborescence récursivement
        return $roots->map(function ($page) use ($children) {
            return self::buildMenuItem($page, $children);
        })->values()->toArray();
    }

    /**
     * Construit un item de menu avec ses enfants.
     * 
     * @param Page $page Page à transformer en item de menu
     * @param Collection<Page> $allChildren Toutes les pages enfants disponibles
     * @return array<string, mixed> Item de menu avec structure
     */
    private static function buildMenuItem(Page $page, Collection $allChildren): array
    {
        $item = [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'url' => route('pages.show', $page->slug),
            'order' => $page->menu_order,
            'children' => [],
        ];

        // Trouver les enfants de cette page
        $pageChildren = $allChildren->filter(fn($child) => $child->parent_id === $page->id);

        if ($pageChildren->isNotEmpty()) {
            // Construire récursivement les enfants
            $item['children'] = $pageChildren
                ->sortBy('menu_order')
                ->map(fn($child) => self::buildMenuItem($child, $allChildren))
                ->values()
                ->toArray();
        }

        return $item;
    }

    /**
     * Vérifie si une page peut être vue par un utilisateur.
     * 
     * @param Page $page Page à vérifier
     * @param User|null $user Utilisateur (null pour invité)
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
     * @param Page $page Page dont on veut les sections
     * @param User|null $user Utilisateur connecté (null pour invité)
     * @return Collection<Section> Collection de sections
     */
    public static function getPublishedSections(Page $page, ?User $user = null): Collection
    {
        return \App\Services\SectionService::getDisplayableSections($page, $user);
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
     * @param User|null $user Utilisateur spécifique (null pour tous les utilisateurs)
     * @return void
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
            Cache::forget('menu_pages_' . $user->id);
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
     * @param string $slug Slug de la page
     * @param User|null $user Utilisateur connecté (null pour invité)
     * @return Page|null Page trouvée ou null
     */
    public static function getPageBySlug(string $slug, ?User $user = null): ?Page
    {
        $page = Page::where('slug', $slug)->first();

        if (!$page || !self::canViewPage($page, $user)) {
            return null;
        }

        // Charger les sections affichables via SectionService
        $sections = \App\Services\SectionService::getDisplayableSections($page, $user);
        $page->setRelation('sections', $sections);

        return $page;
    }

    /**
     * Vérifie si une page peut être affichée dans le menu.
     * 
     * @param Page $page Page à vérifier
     * @param User|null $user Utilisateur connecté (null pour invité)
     * @return bool True si la page peut être dans le menu
     */
    public static function canBeInMenu(Page $page, ?User $user = null): bool
    {
        return $page->isPublished()
            && $page->in_menu
            && $page->isVisibleFor($user);
    }
}

