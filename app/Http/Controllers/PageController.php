<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Services\NotificationService;
use App\Services\PageService;
use Illuminate\Support\Facades\Auth;
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
    public function index()
    {
        $this->authorize('viewAny', \App\Models\Page::class);
        $pages = \App\Models\Page::with(['sections', 'users', 'parent', 'children', 'campaigns', 'scenarios', 'createdBy'])->paginate(20);
        
        // Récupérer toutes les pages pour le select parent_id dans le modal
        $allPages = Page::select('id', 'title', 'slug')
            ->orderBy('title')
            ->get();
        
        return Inertia::render('Pages/page/Index', [
            'pages' => PageResource::collection($pages),
            'allPages' => $allPages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
    public function store(\App\Http\Requests\StorePageRequest $request)
    {
        $this->authorize('create', \App\Models\Page::class);
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $page = \App\Models\Page::create($data);
        $page->load(['sections', 'users', 'parent', 'children', 'campaigns', 'scenarios', 'createdBy']);
        \App\Services\NotificationService::notifyEntityCreated($page, $request->user());
        PageService::clearMenuCache();
        return redirect()->route('pages.show', $page)->with('success', 'Page créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Page $page)
    {
        $this->authorize('view', $page);
        
        // Charger les sections affichables selon l'utilisateur
        $user = auth()->user();
        $sections = PageService::getPublishedSections($page, $user);
        $page->setRelation('sections', $sections);
        
        $page->load(['users', 'parent', 'children', 'campaigns', 'scenarios', 'createdBy']);
        
        // Récupérer toutes les pages pour le select parent_id dans le modal d'édition
        $pages = Page::select('id', 'title', 'slug')
            ->where('id', '!=', $page->id)
            ->orderBy('title')
            ->get();
        
        return Inertia::render('Pages/page/Show', [
            'page' => new PageResource($page),
            'pages' => $pages,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Page $page)
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
    public function update(\App\Http\Requests\UpdatePageRequest $request, \App\Models\Page $page)
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
        return redirect()->route('pages.show', $page)->with('success', 'Page mise à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(\App\Models\Page $page)
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
    public function attachUser(\Illuminate\Http\Request $request, Page $page)
    {
        $this->authorize('update', $page);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $page->users()->attach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Dissocie un utilisateur de la page.
     */
    public function detachUser(\Illuminate\Http\Request $request, Page $page)
    {
        $this->authorize('update', $page);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $page->users()->detach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Synchronise la liste des utilisateurs associés à la page.
     */
    public function syncUsers(\Illuminate\Http\Request $request, Page $page)
    {
        $this->authorize('update', $page);
        $request->validate(['user_ids' => 'array', 'user_ids.*' => 'exists:users,id']);
        $page->users()->sync($request->user_ids);
        return response()->json(['success' => true]);
    }

    /**
     * Liste les utilisateurs associés à la page.
     */
    public function users(Page $page)
    {
        $this->authorize('view', $page);
        return response()->json($page->users);
    }

    public function restore(\App\Models\Page $page)
    {
        $this->authorize('restore', $page);
        $page->restore();
        \App\Services\NotificationService::notifyEntityRestored($page, request()->user());
        PageService::clearMenuCache();
        return redirect()->route('pages.index')->with('success', 'Page restaurée.');
    }

    public function forceDelete(\App\Models\Page $page)
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
    public function menu()
    {
        $user = auth()->user();
        $pages = PageService::getMenuPages($user);
        $menuTree = PageService::buildMenuTree($pages);
        
        return response()->json([
            'menu' => $menuTree,
        ]);
    }
}
