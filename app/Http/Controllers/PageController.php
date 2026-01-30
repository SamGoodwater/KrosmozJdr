<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Services\NotificationService;
use App\Services\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\PageResource;
use Inertia\Inertia;

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
     * @return \Inertia\Response
     */
    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', \App\Models\Page::class);
        
        // OPTIMISATION : Eager loading avec select pour réduire les données
        $pages = \App\Models\Page::with([
            'sections:id,page_id,title,template,state',
            'users:id,name,email',
            'parent:id,title,slug',
            'children:id,parent_id,title,slug',
            // campaigns/scenarios utilisent `name` (pas `title`)
            'campaigns:id,name,slug',
            'scenarios:id,name,slug',
            'createdBy:id,name,email'
        ])->paginate(20);
        
        // OPTIMISATION : Utiliser le cache pour la liste des pages (utilisée dans plusieurs endroits)
        $allPages = \Illuminate\Support\Facades\Cache::remember('pages_select_list', 3600, function () {
            return Page::select('id', 'title', 'slug')
                ->orderBy('title')
                ->get();
        });
        
        return Inertia::render('Pages/page/Index', [
            'pages' => PageResource::collection($pages),
            'allPages' => $allPages,
            'can' => [
                'create' => $request->user()?->can('create', \App\Models\Page::class) ?? false,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        $this->authorize('create', \App\Models\Page::class);
        
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
    public function store(\App\Http\Requests\StorePageRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', \App\Models\Page::class);
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $page = \App\Models\Page::create($data);
        $page->load(['sections', 'users', 'parent', 'children', 'campaigns', 'scenarios', 'createdBy']);
        \App\Services\NotificationService::notifyEntityCreated($page, $request->user());
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
     * @param \App\Models\Page $page Page à afficher (résolue par route model binding via slug)
     * @return \Inertia\Response Vue Inertia avec la page et ses sections
     */
    public function show(\App\Models\Page $page): \Inertia\Response
    {
        // Autoriser les invités si la page est visible pour eux (policy accepte ?User)
        $this->authorize('view', $page);

        $user = auth()->user();
        
        // OPTIMISATION : Charger toutes les relations en une seule requête
        $page->load([
            'users',
            'parent',
            'children',
            'campaigns',
            'scenarios',
            'createdBy'
        ]);
        
        // Charger les sections selon l'utilisateur
        // Si l'utilisateur peut modifier la page, inclure toutes les sections (y compris les drafts)
        // Sinon, inclure uniquement les sections affichables (publiées)
        $sections = \App\Services\SectionService::getSectionsForPage($page, $user);
        
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
                'sections' => $sections->map(fn($s) => [
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
        
        // OPTIMISATION : Charger toutes les pages en cache (utilisé pour le menu ET le modal)
        $pages = \Illuminate\Support\Facades\Cache::remember('pages_select_list', 3600, function () {
            return Page::select('id', 'title', 'slug')
                ->orderBy('title')
                ->get();
        });
        
        // Filtrer la page courante côté PHP (plus rapide que requête SQL)
        $pagesFiltered = $pages->where('id', '!=', $page->id)->values();
        
        return Inertia::render('Pages/page/Show', [
            'page' => new PageResource($page),
            'pages' => $pagesFiltered,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Page $page): \Inertia\Response
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
    public function update(\App\Http\Requests\UpdatePageRequest $request, \App\Models\Page $page): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $page);
        // Créer une copie des attributs avant la mise à jour pour les notifications
        $oldAttributes = $page->getAttributes();
        $data = $request->validated();
        $page->update($data);
        $page->load(['sections', 'users', 'parent', 'children', 'campaigns', 'scenarios', 'createdBy']);
        // Créer un modèle temporaire avec les anciens attributs pour les notifications
        $old = new \App\Models\Page();
        $old->setRawAttributes($oldAttributes);
        $old->exists = true;
        $old->id = $page->id;
        try {
            \App\Services\NotificationService::notifyEntityModified($page, $request->user(), $old);
        } catch (\Exception $e) {
            // Si les notifications échouent, on continue quand même
            \Log::warning('Erreur lors de l\'envoi des notifications pour la page ' . $page->id . ': ' . $e->getMessage());
        }
        PageService::clearMenuCache();
        // Route `pages.show` utilise `{page:slug}` : on redirige explicitement avec le slug.
        return redirect()->route('pages.show', $page->slug)->with('success', 'Page mise à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(\App\Models\Page $page): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $page);
        $user = request()->user();
        $page->delete();
        \App\Services\NotificationService::notifyEntityDeleted($page, $user);
        PageService::clearMenuCache();
        return redirect()->route('pages.index')->with('success', 'Page supprimée.');
    }

    /**
     * Associe un utilisateur à la page.
     */
    public function attachUser(\Illuminate\Http\Request $request, Page $page): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $page);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $page->users()->attach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Dissocie un utilisateur de la page.
     */
    public function detachUser(\Illuminate\Http\Request $request, Page $page): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $page);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $page->users()->detach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Synchronise la liste des utilisateurs associés à la page.
     */
    public function syncUsers(\Illuminate\Http\Request $request, Page $page): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $page);
        $request->validate(['user_ids' => 'array', 'user_ids.*' => 'exists:users,id']);
        $page->users()->sync($request->user_ids);
        return response()->json(['success' => true]);
    }

    /**
     * Liste les utilisateurs associés à la page.
     */
    public function users(Page $page): \Illuminate\Http\JsonResponse
    {
        $this->authorize('view', $page);
        return response()->json($page->users);
    }

    public function restore(int $page): \Illuminate\Http\RedirectResponse
    {
        $model = \App\Models\Page::withTrashed()->findOrFail($page);
        $this->authorize('restore', $model);
        $model->restore();
        \App\Services\NotificationService::notifyEntityRestored($model, request()->user());
        PageService::clearMenuCache();
        return redirect()->route('pages.index')->with('success', 'Page restaurée.');
    }

    public function forceDelete(\App\Models\Page $page): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('forceDelete', $page);
        $page->forceDelete();
        \App\Services\NotificationService::notifyEntityForceDeleted($page, request()->user());
        PageService::clearMenuCache();
        return redirect()->route('pages.index')->with('success', 'Page supprimée définitivement.');
    }

    /**
     * Récupère les pages du menu pour un utilisateur.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function menu(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $pages = PageService::getMenuPages($user);
        $menuTree = PageService::buildMenuTree($pages);
        
        return response()->json([
            'menu' => $menuTree,
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
     * @param \Illuminate\Http\Request $request Requête contenant le tableau de pages
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec success: true
     * @throws \Illuminate\Auth\Access\AuthorizationException Si l'utilisateur n'a pas les droits
     */
    public function reorder(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Page::class);

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
            if (!$page) {
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
}
