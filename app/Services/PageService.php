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
     * Organise les pages en structure hiérarchique (parent/children).
     * 
     * @param Collection<Page> $pages Collection de pages
     * @return array<int, array<string, mixed>> Arborescence du menu
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
        return $page->sections()
            ->displayable($user)
            ->get();
    }

    /**
     * Invalide le cache des pages du menu.
     * 
     * À appeler après modification d'une page (création, mise à jour, suppression).
     * 
     * @param User|null $user Utilisateur spécifique (null pour tous les utilisateurs)
     * @return void
     */
    public static function clearMenuCache(?User $user = null): void
    {
        if ($user) {
            Cache::forget('menu_pages_' . $user->id);
        } else {
            // Invalider pour tous les utilisateurs (pattern matching)
            Cache::flush(); // Ou utiliser un système de tags si disponible
        }
        
        // Toujours invalider pour les invités
        Cache::forget('menu_pages_guest');
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

        // Charger les sections affichables
        $page->load(['sections' => function ($query) use ($user) {
            $query->displayable($user);
        }]);

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

