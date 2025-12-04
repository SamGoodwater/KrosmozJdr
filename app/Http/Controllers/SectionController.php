<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Models\File;
use App\Services\FileService;
use App\Services\ImageService;
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
    public function index()
    {
        $this->authorize('viewAny', \App\Models\Section::class);
        $sections = \App\Models\Section::with(['page', 'users', 'files', 'createdBy'])->paginate(20);
        return Inertia::render('Pages/section/Index', [
            'sections' => SectionResource::collection($sections),
        ]);
    }

    /**
     * Affiche le formulaire de création d'une section.
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->authorize('create', \App\Models\Section::class);
        
        // Récupérer toutes les pages pour le select
        $pages = \App\Models\Page::select('id', 'title', 'slug')
            ->orderBy('title')
            ->get();
        
        return Inertia::render('Pages/section/Create', [
            'pages' => $pages,
        ]);
    }

    /**
     * Enregistre une nouvelle section.
     * @param StoreSectionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(\App\Http\Requests\StoreSectionRequest $request)
    {
        $this->authorize('create', \App\Models\Section::class);
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $section = \App\Models\Section::create($data);
        $section->load(['page', 'users', 'files', 'createdBy']);
        \App\Services\NotificationService::notifyEntityCreated($section, $request->user());
        return redirect()->route('sections.show', $section)->with('success', 'Section créée avec succès.');
    }

    /**
     * Affiche une section spécifique.
     * @param Section $section
     * @return \Inertia\Response
     */
    public function show(\App\Models\Section $section)
    {
        $this->authorize('view', $section);
        $section->load(['page', 'users', 'files', 'createdBy']);
        return Inertia::render('Pages/section/Show', [
            'section' => new SectionResource($section),
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une section.
     * @param Section $section
     * @return \Inertia\Response
     */
    public function edit(\App\Models\Section $section)
    {
        $this->authorize('update', $section);
        $section->load(['page', 'users', 'files', 'createdBy']);
        
        // Récupérer toutes les pages pour le select
        $pages = \App\Models\Page::select('id', 'title', 'slug')
            ->orderBy('title')
            ->get();
        
        return Inertia::render('Pages/section/Edit', [
            'section' => new SectionResource($section),
            'pages' => $pages,
        ]);
    }

    /**
     * Met à jour une section existante.
     * @param UpdateSectionRequest $request
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(\App\Http\Requests\UpdateSectionRequest $request, \App\Models\Section $section)
    {
        $this->authorize('update', $section);
        // Créer une copie des attributs avant la mise à jour pour les notifications
        $oldAttributes = $section->getAttributes();
        $data = $request->validated();
        $section->update($data);
        $section->load(['page', 'users', 'files', 'createdBy']);
        // Créer un modèle temporaire avec les anciens attributs pour les notifications
        $old = new \App\Models\Section();
        $old->setRawAttributes($oldAttributes);
        $old->exists = true;
        $old->id = $section->id;
        try {
            \App\Services\NotificationService::notifyEntityModified($section, $request->user(), $old);
        } catch (\Exception $e) {
            // Si les notifications échouent, on continue quand même
            \Log::warning('Erreur lors de l\'envoi des notifications pour la section ' . $section->id . ': ' . $e->getMessage());
        }
        return redirect()->route('sections.show', $section)->with('success', 'Section mise à jour.');
    }

    /**
     * Supprime une section (soft delete).
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(\App\Models\Section $section)
    {
        $this->authorize('delete', $section);
        $user = request()->user();
        $section->delete();
        \App\Services\NotificationService::notifyEntityDeleted($section, $user);
        return redirect()->route('sections.index')->with('success', 'Section supprimée.');
    }

    /**
     * Ajoute un fichier à une section.
     * @param StoreFileRequest $request
     * @param Section $section
     * @param ImageService $imageService
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeFile(StoreFileRequest $request, Section $section, ImageService $imageService)
    {
        // Autorisation (policy sur la section)
        Gate::authorize('update', $section);

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
    public function deleteFile(Section $section, File $file)
    {
        // Autorisation (policy sur la section)
        Gate::authorize('update', $section);

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
    public function attachUser(\Illuminate\Http\Request $request, Section $section)
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
    public function detachUser(\Illuminate\Http\Request $request, Section $section)
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
    public function syncUsers(\Illuminate\Http\Request $request, Section $section)
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
    public function users(Section $section)
    {
        $this->authorize('view', $section);
        return response()->json($section->users);
    }

    /**
     * Restaure une section supprimée.
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(\App\Models\Section $section)
    {
        $this->authorize('restore', $section);
        $section->restore();
        \App\Services\NotificationService::notifyEntityRestored($section, request()->user());
        return redirect()->route('sections.index')->with('success', 'Section restaurée.');
    }

    /**
     * Supprime définitivement une section.
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(\App\Models\Section $section)
    {
        $this->authorize('forceDelete', $section);
        $section->forceDelete();
        \App\Services\NotificationService::notifyEntityForceDeleted($section, request()->user());
        return redirect()->route('sections.index')->with('success', 'Section supprimée définitivement.');
    }

    /**
     * Réorganise l'ordre des sections (drag & drop).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Section::class);

        $data = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.id' => ['required', 'integer', 'exists:sections,id'],
            'sections.*.order' => ['required', 'integer', 'min:0'],
        ]);

        $sections = Section::whereIn('id', collect($data['sections'])->pluck('id'))->get();

        foreach ($data['sections'] as $item) {
            $section = $sections->firstWhere('id', $item['id']);
            if (!$section) {
                continue;
            }

            // Vérifier l'autorisation de mise à jour pour chaque section
            $this->authorize('update', $section);

            $section->update([
                'order' => $item['order'],
            ]);
        }

        return response()->json(['success' => true]);
    }
}
