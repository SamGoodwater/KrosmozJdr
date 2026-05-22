<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Resources\Entity\BreedResource;
use App\Http\Resources\Entity\SpecializationResource;
use App\Http\Resources\PageResource;
use App\Models\Entity\Breed;
use App\Models\Entity\Specialization;
use App\Models\Page;
use App\Models\User;
use App\Services\BibliothequeEntityPageService;
use App\Services\NotificationService;
use App\Services\PageService;
use App\Services\SectionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Contrôleur de gestion des pages dynamiques (CRUD, associations, notifications).
 *
 * Gère la création, l'affichage, la modification, la suppression, la restauration et la gestion des utilisateurs associés aux pages.
 * Toutes les méthodes respectent les policies et envoient des notifications métier.
 */
class PageController extends Controller
{
    /**
     * Affiche la liste paginée des pages.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Page::class);

        // OPTIMISATION : Eager loading avec select pour réduire les données
        $pages = Page::with([
            'sections:id,page_id,title,template,state',
            'users:id,name,email',
            'parent:id,title,slug',
            'children:id,parent_id,title,slug',
            // campaigns/scenarios utilisent `name` (pas `title`)
            'campaigns:id,name,slug',
            'scenarios:id,name,slug',
            'createdBy:id,name,email',
        ])->paginate(20);

        $allPages = PageService::getPagesSelectList();

        return Inertia::render('Pages/page/Index', [
            'pages' => PageResource::collection($pages),
            'allPages' => $allPages,
            'can' => [
                'create' => $request->user()?->can('create', Page::class) ?? false,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Page::class);

        // Récupérer toutes les pages pour le select parent_id
        $pages = Page::select('id', 'title', 'slug')
            ->orderBy('title')
            ->get();

        return Inertia::render('Pages/page/Create', [
            'pages' => $pages,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request): RedirectResponse
    {
        $this->authorize('create', Page::class);
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $page = Page::create($data);
        $page->load(['sections', 'users', 'parent', 'children', 'campaigns', 'scenarios', 'createdBy']);
        NotificationService::notifyEntityCreated($page, $request->user());
        PageService::clearMenuCache();

        // Route `pages.show` utilise `{page:slug}` : on redirige explicitement avec le slug.
        return redirect()->route('pages.show', $page->slug)->with('success', 'Page créée avec succès.');
    }

    /**
     * Affiche une page avec ses sections.
     *
     * **Logique de chargement des sections :**
     * - Si l'utilisateur peut modifier la page : charge TOUTES les sections (drafts inclus)
     *   → Permet d'éditer toutes les sections, même non publiées
     * - Sinon : charge uniquement les sections affichables (publiées + visibles)
     *   → Respecte la visibilité et l'état pour les utilisateurs sans droits d'édition
     *
     * **Permissions :**
     * - Utilise la policy `PagePolicy::view()` pour vérifier les droits
     * - Autorise les invités si la page est visible pour eux
     *
     * @param  Page  $page  Page à afficher (résolue par route model binding via slug)
     * @return Response Vue Inertia avec la page et ses sections
     */
    public function show(Page $page): Response
    {
        // Autoriser les invités si la page est visible pour eux (policy accepte ?User)
        $this->authorize('view', $page);

        $user = auth()->user();

        $linked = $page->settings['linked_entity'] ?? null;
        if (is_array($linked) && ! empty($linked['type']) && ! empty($linked['id'])) {
            return $this->renderLinkedEntityPage($page, $linked, $user);
        }

        // OPTIMISATION : Charger toutes les relations en une seule requête
        $page->load([
            'users',
            'parent',
            'children',
            'campaigns',
            'scenarios',
            'createdBy',
        ]);

        // Charger les sections selon l'utilisateur
        // Si l'utilisateur peut modifier la page, inclure toutes les sections (y compris les drafts)
        // Sinon, inclure uniquement les sections affichables (publiées)
        $sections = SectionService::getSectionsForPage($page, $user);

        // OPTIMISATION : Éviter le N+1 - la page est déjà chargée
        // On utilise setRelation pour associer la page à chaque section sans requête supplémentaire
        $sections->each(function ($section) use ($page) {
            $section->setRelation('page', $page);
        });

        // Debug en développement
        if (config('app.debug')) {
            \Log::debug('PageController::show - Sections loaded', [
                'page_id' => $page->id,
                'user_id' => $user?->id,
                'can_update_page' => $user ? $user->can('update', $page) : false,
                'sections_count' => $sections->count(),
                'sections' => $sections->map(fn ($s) => [
                    'id' => $s->id,
                    'template' => $s->template->value ?? $s->template,
                    'state' => $s->state,
                    'read_level' => $s->read_level ?? null,
                    'write_level' => $s->write_level ?? null,
                    'page_read_level' => $s->page ? ($s->page->read_level ?? null) : null,
                    'page_write_level' => $s->page ? ($s->page->write_level ?? null) : null,
                    'can_be_edited_by' => $user ? $s->canBeEditedBy($user) : false,
                ])->toArray(),
            ]);
        }

        $page->setRelation('sections', $sections);

        $pages = collect(PageService::getPagesSelectList());

        // Filtrer la page courante côté PHP (plus rapide que requête SQL)
        $pagesFiltered = $pages->where('id', '!=', $page->id)->values();

        return Inertia::render('Pages/page/Show', [
            'page' => new PageResource($page),
            'pages' => $pagesFiltered,
            'menuChildIndex' => PageService::buildMenuChildIndex($page, $user),
        ]);
    }

    /**
     * Affiche une page CMS liée à une fiche classe ou spécialisation (sous-page bibliothèque).
     *
     * @param  array{type?: string, id?: int}  $linked
     */
    private function renderLinkedEntityPage(Page $page, array $linked, ?User $user): Response
    {
        $entity = app(BibliothequeEntityPageService::class)->resolveLinkedEntity($linked);
        if ($entity === null) {
            abort(404);
        }

        $this->authorize('view', $entity);

        $page->load(['users', 'parent', 'children', 'campaigns', 'scenarios', 'createdBy']);
        $sections = SectionService::getSectionsForPage($page, $user);
        $sections->each(fn ($section) => $section->setRelation('page', $page));
        $page->setRelation('sections', $sections);

        $pages = collect(PageService::getPagesSelectList());
        $pagesFiltered = $pages->where('id', '!=', $page->id)->values();

        $linkedType = (string) ($linked['type'] ?? '');
        $payload = [
            'page' => new PageResource($page),
            'pages' => $pagesFiltered,
            'linkedEntityType' => $linkedType,
        ];

        if ($entity instanceof Breed) {
            $entity->load([
                'createdBy',
                'elementOrientations',
                'spells' => fn ($q) => $q->orderBy('breed_spell.character_level')
                    ->orderBy('breed_spell.slot_index')
                    ->orderBy('breed_spell.choice_order')
                    ->orderBy('spells.name'),
                'npcs' => fn ($q) => $q->limit(100),
                'capabilities' => fn ($q) => $q->orderBy('name'),
                'creatureTraits' => fn ($q) => $q->orderBy('name'),
                'languages',
                'sections' => Breed::orderedSectionsEagerLoadConstraint(),
            ]);
            $payload['linkedEntity'] = new BreedResource($entity);
        } else {
            /** @var Specialization $entity */
            $entity->load([
                'createdBy',
                'npcs' => fn ($q) => $q->limit(100),
                'capabilities' => fn ($q) => $q->orderBy('name'),
                'spells' => fn ($q) => $q->orderBy('name'),
                'creatureTraits' => fn ($q) => $q->orderBy('name'),
                'consumables' => fn ($q) => $q->orderBy('name'),
                'resources' => fn ($q) => $q->orderBy('name'),
                'items' => fn ($q) => $q->orderBy('name'),
                'sections' => Specialization::orderedSectionsEagerLoadConstraint(),
            ]);
            $payload['linkedEntity'] = new SpecializationResource($entity);
        }

        return Inertia::render('Pages/page/LinkedEntityShow', $payload);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page): Response
    {
        $this->authorize('update', $page);
        $page->load(['sections', 'users', 'parent', 'children', 'campaigns', 'scenarios', 'createdBy']);

        // Récupérer toutes les pages pour le select parent_id (exclure la page courante)
        $pages = Page::select('id', 'title', 'slug')
            ->where('id', '!=', $page->id)
            ->orderBy('title')
            ->get();

        return Inertia::render('Pages/page/Edit', [
            'page' => new PageResource($page),
            'pages' => $pages,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $this->authorize('update', $page);
        // Créer une copie des attributs avant la mise à jour pour les notifications
        $oldAttributes = $page->getAttributes();
        $data = $request->validated();
        $page->update($data);
        $page->load(['sections', 'users', 'parent', 'children', 'campaigns', 'scenarios', 'createdBy']);
        // Créer un modèle temporaire avec les anciens attributs pour les notifications
        $old = new Page;
        $old->setRawAttributes($oldAttributes);
        $old->exists = true;
        $old->id = $page->id;
        try {
            NotificationService::notifyEntityModified($page, $request->user(), $old);
        } catch (\Exception $e) {
            // Si les notifications échouent, on continue quand même
            \Log::warning('Erreur lors de l\'envoi des notifications pour la page '.$page->id.': '.$e->getMessage());
        }
        PageService::clearMenuCache();

        // Route `pages.show` utilise `{page:slug}` : on redirige explicitement avec le slug.
        return redirect()->route('pages.show', $page->slug)->with('success', 'Page mise à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);
        $user = $this->authenticatedUser();
        $page->delete();
        NotificationService::notifyEntityDeleted($page, $user);
        PageService::clearMenuCache();

        return redirect()->route('pages.index')->with('success', 'Page supprimée.');
    }

    /**
     * Associe un utilisateur à la page.
     */
    public function attachUser(Request $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $page->users()->attach($request->user_id);

        return response()->json(['success' => true]);
    }

    /**
     * Dissocie un utilisateur de la page.
     */
    public function detachUser(Request $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $page->users()->detach($request->user_id);

        return response()->json(['success' => true]);
    }

    /**
     * Synchronise la liste des utilisateurs associés à la page.
     */
    public function syncUsers(Request $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);
        $request->validate(['user_ids' => 'array', 'user_ids.*' => 'exists:users,id']);
        $page->users()->sync($request->user_ids);

        return response()->json(['success' => true]);
    }

    /**
     * Liste les utilisateurs associés à la page.
     */
    public function users(Page $page): JsonResponse
    {
        $this->authorize('view', $page);

        return response()->json($page->users);
    }

    public function restore(int $page): RedirectResponse
    {
        $model = Page::withTrashed()->findOrFail($page);
        $this->authorize('restore', $model);
        $model->restore();
        NotificationService::notifyEntityRestored($model, $this->authenticatedUser());
        PageService::clearMenuCache();

        return redirect()->route('pages.index')->with('success', 'Page restaurée.');
    }

    public function forceDelete(Page $page): RedirectResponse
    {
        $this->authorize('forceDelete', $page);
        $page->forceDelete();
        NotificationService::notifyEntityForceDeleted($page, $this->authenticatedUser());
        PageService::clearMenuCache();

        return redirect()->route('pages.index')->with('success', 'Page supprimée définitivement.');
    }

    /**
     * Récupère les pages du menu pour un utilisateur.
     */
    public function menu(): JsonResponse
    {
        $user = auth()->user();
        $pages = PageService::getMenuPages($user);
        $menuTree = PageService::buildMenuTree($pages);

        $tree = collect($menuTree);
        $reglesItems = $tree->filter(fn ($p) => ($p['menu_group'] ?? '') === 'Règles')->sortBy('order')->values()->toArray();
        $referentielsItems = $tree->filter(fn ($p) => ($p['menu_group'] ?? '') === 'L\'Essentiels')->sortBy('order')->values()->toArray();
        $informationsItems = $tree->filter(fn ($p) => ($p['menu_group'] ?? '') === 'Informations')->sortBy('order')->values()->toArray();
        $bibliothequesItems = $tree->filter(fn ($p) => ($p['menu_group'] ?? '') === 'Bibliothèques')->sortBy('order')->values()->toArray();
        if ($bibliothequesItems === []) {
            // Compatibilité ascendante : fallback config si les pages Bibliothèques ne sont pas seedées.
            $bibliothequesItems = collect(self::normalizeNavBibliothequeEntries(config('nav_menu.bibliotheques', [])))
                ->sortBy('order')
                ->map(fn (array $item) => [
                    'id' => 'bibliotheque-'.($item['route'] ?? ($item['label'] ?? 'item')),
                    'title' => $item['label'],
                    'url' => self::bibliothequeMenuItemUrl($item),
                    'entity_key' => $item['entity_key'] ?? null,
                    'order' => $item['order'] ?? 0,
                    'menu_item_css_classes' => $item['menu_item_css_classes']
                        ?? (($item['entity_key'] ?? null) ? 'color-'.$item['entity_key'].'-500 box-shadow-glass' : null),
                    'children' => [],
                ])
                ->values()
                ->toArray();
        }

        $allGroups = [
            ['id' => 'referentiels', 'title' => 'L\'Essentiels', 'menu_group' => 'L\'Essentiels', 'order' => 0, 'icon' => 'fa-book-bookmark', 'children' => $referentielsItems],
            ['id' => 'regles', 'title' => 'Règles', 'menu_group' => 'Règles', 'order' => 1, 'icon' => 'fa-book', 'children' => $reglesItems],
            ['id' => 'bibliotheques', 'title' => 'Bibliothèques', 'menu_group' => 'Bibliothèques', 'order' => 2, 'icon' => 'fa-book-open-reader', 'children' => $bibliothequesItems],
            ['id' => 'informations', 'title' => 'Informations', 'menu_group' => 'Informations', 'order' => 4, 'icon' => 'fa-circle-info', 'children' => $informationsItems],
        ];

        $menu = collect($allGroups)
            ->filter(fn (array $group) => count($group['children']) > 0)
            ->sortBy('order')
            ->values()
            ->toArray();

        return response()->json([
            'menu' => $menu,
        ]);
    }

    /**
     * Réorganise l'ordre des pages dans le menu (drag & drop).
     *
     * **Fonctionnement :**
     * - Reçoit un tableau de pages avec leur nouvel ordre
     * - Met à jour le champ `menu_order` de chaque page
     * - Vérifie les permissions pour chaque page individuellement
     * - Invalide le cache du menu après modification
     *
     * **Format de la requête :**
     * ```json
     * {
     *   "pages": [
     *     {"id": 1, "menu_order": 1},
     *     {"id": 2, "menu_order": 2},
     *     {"id": 3, "menu_order": 3}
     *   ]
     * }
     * ```
     *
     * @param  Request  $request  Requête contenant le tableau de pages
     * @return JsonResponse Réponse JSON avec success: true
     *
     * @throws AuthorizationException Si l'utilisateur n'a pas les droits
     */
    public function reorder(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Page::class);

        $data = $request->validate([
            'pages' => ['required', 'array'],
            'pages.*.id' => ['required', 'integer', 'exists:pages,id'],
            'pages.*.menu_order' => ['required', 'integer', 'min:0'],
        ]);

        /** @var array<int, array{id:int, menu_order:int}> $items */
        $items = $data['pages'];

        $pageIds = array_map(static fn (array $item): int => (int) $item['id'], $items);

        // Récupérer toutes les pages en une seule requête pour optimiser
        $pages = Page::whereIn('id', $pageIds)->get();

        foreach ($items as $item) {
            $page = $pages->firstWhere('id', $item['id']);
            if (! $page) {
                continue;
            }

            // Vérifier l'autorisation de mise à jour pour chaque page individuellement
            $this->authorize('update', $page);

            $page->update([
                'menu_order' => $item['menu_order'],
            ]);
        }

        // Invalider le cache du menu après modification
        PageService::clearMenuCache();

        return response()->json(['success' => true]);
    }

    /**
     * Utilisateur authentifié (méthodes derrière le middleware `auth`).
     */
    private function authenticatedUser(): User
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }

    /**
     * URL d'une entrée menu « Bibliothèques » (config ou fallback).
     */
    private static function bibliothequeMenuItemUrl(array $item): string
    {
        if (isset($item['url'])) {
            return (string) $item['url'];
        }

        $routeName = $item['route'] ?? null;
        if (! is_string($routeName) || $routeName === '' || ! Route::has($routeName)) {
            return '#';
        }

        return route($routeName, $item['route_params'] ?? [], false);
    }

    /**
     * Filtre la configuration du menu « Bibliothèques » pour un flux Collection analysable statiquement.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function normalizeNavBibliothequeEntries(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $normalized = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                $normalized[] = $item;
            }
        }

        return $normalized;
    }
}
