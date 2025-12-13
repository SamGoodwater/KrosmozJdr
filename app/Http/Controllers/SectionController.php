<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Page;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Models\File;
use App\Services\FileService;
use App\Services\ImageService;
use App\Services\SectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Http\Resources\SectionResource;

/**
 * Contrôleur de gestion des sections dynamiques (CRUD, fichiers, associations, notifications).
 *
 * Gère la création, l'affichage, la modification, la suppression, la restauration et la gestion des utilisateurs et fichiers associés aux sections.
 * Toutes les méthodes respectent les policies et envoient des notifications métier.
 */
class SectionController extends Controller
{

    /**
     * Affiche la liste paginée des sections.
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $this->authorize('viewAny', \App\Models\Section::class);
        $sections = \App\Models\Section::with(['page', 'users', 'files', 'createdBy'])->paginate(20);
        return Inertia::render('Pages/section/Index', [
            'sections' => SectionResource::collection($sections),
        ]);
    }

    /**
     * Affiche le formulaire de création d'une section.
     * @return \Illuminate\Http\RedirectResponse
     * @deprecated Utiliser le modal CreateSectionModal depuis la page
     */
    public function create(): \Illuminate\Http\RedirectResponse
    {
        // Rediriger vers la liste des pages
        return redirect()->route('pages.index');
    }

    /**
     * Enregistre une nouvelle section.
     * 
     * **Flux :**
     * 1. Validation des données via `StoreSectionRequest`
     * 2. Création de la section via `SectionService::create()` (avec valeurs par défaut)
     * 3. Envoi d'une notification de création
     * 4. Redirection vers la page parente (pour afficher la nouvelle section)
     * 
     * **Valeurs par défaut :**
     * - L'ordre est calculé automatiquement (dernière position)
     * - Les valeurs par défaut du template sont appliquées
     * - État initial : `draft`
     * - Visibilité initiale : `guest`
     * 
     * @param StoreSectionRequest $request Requête validée contenant les données de la section
     * @return \Illuminate\Http\RedirectResponse Redirection vers la page parente
     * @throws \Illuminate\Auth\Access\AuthorizationException Si l'utilisateur n'a pas les droits
     */
    public function store(\App\Http\Requests\StoreSectionRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();

        $page = Page::findOrFail($data['page_id']);
        $this->authorize('create', [\App\Models\Section::class, $page]);
        
        // Création via le service (gère les valeurs par défaut et la transaction)
        $section = SectionService::create($data, $request->user());
        
        // Notification de création
        NotificationService::notifyEntityCreated($section, $request->user());
        
        // Toujours rediriger vers la page parente avec Inertia
        // Inertia gère automatiquement les requêtes AJAX
        $page = $section->page;
        return redirect()->route('pages.show', $page->slug)->with('success', 'Section créée avec succès.');
    }

    /**
     * Affiche une section spécifique.
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     * @deprecated Les sections sont affichées dans leur page parente
     */
    public function show(\App\Models\Section $section): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('view', $section);
        
        $page = $section->page;
        return redirect()->route('pages.show', $page->slug)->withFragment('section-' . $section->id);
    }

    /**
     * Affiche le formulaire de modification d'une section.
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     * @deprecated Utiliser le modal SectionParamsModal depuis la page
     */
    public function edit(\App\Models\Section $section): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $section);
        $page = $section->page;
        return redirect()->route('pages.show', $page->slug)->withFragment('section-' . $section->id);
    }

    /**
     * Met à jour une section existante.
     * 
     * **Flux :**
     * 1. Validation des données via `UpdateSectionRequest`
     * 2. Sauvegarde des anciens attributs pour les notifications
     * 3. Mise à jour via `SectionService::update()` (fusion des settings/data)
     * 4. Envoi d'une notification de modification (avec anciens/nouveaux attributs)
     * 5. Redirection vers la page parente
     * 
     * **Fusion des données :**
     * - Les `settings` et `data` sont fusionnés avec les valeurs existantes
     * - Permet de mettre à jour seulement une partie des données sans perdre le reste
     * 
     * @param UpdateSectionRequest $request Requête validée contenant les données à mettre à jour
     * @param Section $section Section à mettre à jour (résolue par route model binding)
     * @return \Illuminate\Http\RedirectResponse Redirection vers la page parente
     * @throws \Illuminate\Auth\Access\AuthorizationException Si l'utilisateur n'a pas les droits
     */
    public function update(\App\Http\Requests\UpdateSectionRequest $request, \App\Models\Section $section): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $section);
        
        // Créer une copie des attributs avant la mise à jour pour les notifications
        $oldAttributes = $section->getAttributes();
        
        // Mise à jour via le service (gère la fusion et la transaction)
        $section = SectionService::update($section, $request->validated(), $request->user());
        
        // Créer un modèle temporaire avec les anciens attributs pour les notifications
        $old = new \App\Models\Section();
        $old->setRawAttributes($oldAttributes);
        $old->exists = true;
        $old->id = $section->id;
        
        try {
            NotificationService::notifyEntityModified($section, $request->user(), $old);
        } catch (\Exception $e) {
            // Si les notifications échouent, on continue quand même (non bloquant)
            \Log::warning('Erreur lors de l\'envoi des notifications pour la section ' . $section->id . ': ' . $e->getMessage());
        }
        
        // Toujours rediriger vers la page parente avec Inertia
        $page = $section->page;
        return redirect()->route('pages.show', $page->slug)->with('success', 'Section mise à jour.');
    }

    /**
     * Supprime une section (soft delete).
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(\App\Models\Section $section): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $section);
        
        SectionService::delete($section, request()->user());
        NotificationService::notifyEntityDeleted($section, request()->user());
        
        return redirect()->route('sections.index')->with('success', 'Section supprimée.');
    }

    /**
     * Ajoute un fichier à une section.
     * @param StoreFileRequest $request
     * @param Section $section
     * @param ImageService $imageService
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeFile(StoreFileRequest $request, Section $section, ImageService $imageService): \Illuminate\Http\JsonResponse
    {
        // Autorisation (policy sur la section)
        $this->authorize('update', $section);

        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('sections', FileService::DISK_DEFAULT);

        // Traitement (ex: conversion webp si image)
        if (FileService::isImagePath($path)) {
            $path = $imageService->convertToWebp($path);
        }

        // Création de l'entrée File
        $file = File::create([
            'file' => $path,
            'title' => $request->input('title'),
            'comment' => $request->input('comment'),
            'description' => $request->input('description'),
        ]);

        // Association à la section (avec ordre si fourni)
        $section->files()->attach($file->id, [
            'order' => $request->input('order'),
        ]);

        return response()->json(['success' => true, 'file' => $file]);
    }

    /**
     * Supprime un fichier lié à une section.
     * @param Section $section
     * @param File $file
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile(Section $section, File $file): \Illuminate\Http\JsonResponse
    {
        // Autorisation (policy sur la section)
        $this->authorize('update', $section);

        // Détacher le fichier de la section
        $section->files()->detach($file->id);

        // (Optionnel) Supprimer le fichier physique et l'entrée File si plus utilisé ailleurs
        if ($file->sections()->count() === 0) {
            Storage::disk(FileService::DISK_DEFAULT)->delete($file->file);
            $file->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Associe un utilisateur à la section.
     * @param \Illuminate\Http\Request $request
     * @param Section $section
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachUser(\Illuminate\Http\Request $request, Section $section): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $section);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $section->users()->attach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Dissocie un utilisateur de la section.
     * @param \Illuminate\Http\Request $request
     * @param Section $section
     * @return \Illuminate\Http\JsonResponse
     */
    public function detachUser(\Illuminate\Http\Request $request, Section $section): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $section);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $section->users()->detach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Synchronise la liste des utilisateurs associés à la section.
     * @param \Illuminate\Http\Request $request
     * @param Section $section
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncUsers(\Illuminate\Http\Request $request, Section $section): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $section);
        $request->validate(['user_ids' => 'array', 'user_ids.*' => 'exists:users,id']);
        $section->users()->sync($request->user_ids);
        return response()->json(['success' => true]);
    }

    /**
     * Liste les utilisateurs associés à la section.
     * @param Section $section
     * @return \Illuminate\Http\JsonResponse
     */
    public function users(Section $section): \Illuminate\Http\JsonResponse
    {
        $this->authorize('view', $section);
        return response()->json($section->users);
    }

    /**
     * Restaure une section supprimée.
     * @param int $section ID de la section (soft-deleted)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(int $section): \Illuminate\Http\RedirectResponse
    {
        $model = \App\Models\Section::withTrashed()->findOrFail($section);
        $this->authorize('restore', $model);
        $model->restore();
        \App\Services\NotificationService::notifyEntityRestored($model, request()->user());
        return redirect()->route('sections.index')->with('success', 'Section restaurée.');
    }

    /**
     * Supprime définitivement une section.
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(\App\Models\Section $section): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('forceDelete', $section);
        $section->forceDelete();
        \App\Services\NotificationService::notifyEntityForceDeleted($section, request()->user());
        return redirect()->route('sections.index')->with('success', 'Section supprimée définitivement.');
    }

    /**
     * Réorganise l'ordre des sections (drag & drop).
     * 
     * **Fonctionnement :**
     * - Reçoit un tableau de sections avec leur nouvel ordre
     * - Met à jour le champ `order` de chaque section
     * - Vérifie les permissions pour chaque section individuellement
     * - Utilise une transaction pour garantir la cohérence
     * 
     * **Format de la requête :**
     * ```json
     * {
     *   "sections": [
     *     {"id": 1, "order": 1},
     *     {"id": 2, "order": 2},
     *     {"id": 3, "order": 3}
     *   ]
     * }
     * ```
     * 
     * @param Request $request Requête contenant le tableau de sections
     * @return \Illuminate\Http\JsonResponse Réponse JSON avec success: true
     * @throws \Illuminate\Auth\Access\AuthorizationException Si l'utilisateur n'a pas les droits
     */
    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Section::class);

        $data = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.id' => ['required', 'integer', 'exists:sections,id'],
            'sections.*.order' => ['required', 'integer', 'min:0'],
        ]);

        /** @var array<int, array{id:int, order:int}> $items */
        $items = $data['sections'];

        $sectionIds = array_map(static fn (array $item): int => (int) $item['id'], $items);

        // Récupérer toutes les sections en une seule requête pour optimiser
        $sections = Section::whereIn('id', $sectionIds)->get();
        
        // Vérifier les autorisations pour chaque section individuellement
        foreach ($sections as $section) {
            $this->authorize('update', $section);
        }

        // Réorganisation via le service (gère la transaction)
        SectionService::reorder($items, $request->user());

        return response()->json(['success' => true]);
    }
}
