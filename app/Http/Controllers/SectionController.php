<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Page;
use App\Models\Section;
use App\Services\NotificationService;
use App\Services\SectionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Section::class);
        $sections = Section::with(['page', 'users', 'media', 'createdBy'])->paginate(20);

        return Inertia::render('Pages/section/Index', [
            'sections' => SectionResource::collection($sections),
        ]);
    }

    /**
     * Affiche le formulaire de création d'une section.
     *
     * @deprecated Utiliser le modal CreateSectionModal depuis la page
     */
    public function create(): RedirectResponse
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
     * @param  StoreSectionRequest  $request  Requête validée contenant les données de la section
     * @return RedirectResponse Redirection vers la page parente après création
     *
     * @throws AuthorizationException Si l'utilisateur n'a pas les droits
     */
    public function store(StoreSectionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $page = Page::findOrFail($data['page_id']);
        $this->authorize('create', [Section::class, $page]);

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
     */
    public function show(Section $section): Response
    {
        $this->authorize('view', $section);

        // Les sections sont généralement affichées dans leur page parente,
        // mais on conserve une vue dédiée (utile pour les tests et liens directs).
        $section->load(['page', 'users', 'media', 'createdBy']);

        return Inertia::render('Pages/section/Show', [
            'section' => new SectionResource($section),
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une section.
     *
     * @deprecated Utiliser le modal SectionParamsModal depuis la page
     */
    public function edit(Section $section): RedirectResponse
    {
        $this->authorize('update', $section);
        $page = $section->page;

        return redirect()->route('pages.show', $page->slug)->withFragment('section-'.$section->id);
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
     * @param  UpdateSectionRequest  $request  Requête validée contenant les données à mettre à jour
     * @param  Section  $section  Section à mettre à jour (résolue par route model binding)
     * @return RedirectResponse Redirection vers la page parente
     *
     * @throws AuthorizationException Si l'utilisateur n'a pas les droits
     */
    public function update(UpdateSectionRequest $request, Section $section): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $section);

        // Créer une copie des attributs avant la mise à jour pour les notifications
        $oldAttributes = $section->getAttributes();

        // Mise à jour via le service (gère la fusion et la transaction)
        $section = SectionService::update($section, $request->validated(), $request->user());

        // Créer un modèle temporaire avec les anciens attributs pour les notifications
        $old = new Section;
        $old->setRawAttributes($oldAttributes);
        $old->exists = true;
        $old->id = $section->id;

        try {
            NotificationService::notifyEntityModified($section, $request->user(), $old);
        } catch (\Exception $e) {
            // Si les notifications échouent, on continue quand même (non bloquant)
            \Log::warning('Erreur lors de l\'envoi des notifications pour la section '.$section->id.': '.$e->getMessage());
        }

        if ($request->expectsJson()) {
            $section->load(['page', 'users', 'media', 'createdBy']);

            return response()->json([
                'success' => true,
                'message' => 'Section mise à jour.',
                'section' => new SectionResource($section),
            ]);
        }

        // Toujours rediriger vers la page parente avec Inertia
        $page = $section->page;

        return redirect()->route('pages.show', $page->slug)->with('success', 'Section mise à jour.');
    }

    /**
     * Supprime une section (soft delete).
     */
    public function delete(Section $section): RedirectResponse
    {
        $this->authorize('delete', $section);
        $page = $section->page;

        SectionService::delete($section, request()->user());
        NotificationService::notifyEntityDeleted($section, request()->user());

        if ($page && $page->slug) {
            return redirect()
                ->route('pages.show', $page->slug)
                ->with('success', 'Section supprimée.');
        }

        return redirect()->route('sections.index')->with('success', 'Section supprimée.');
    }

    /**
     * Ajoute un fichier à une section (Spatie Media Library).
     * Les images sont converties en WebP et une miniature est générée.
     */
    public function storeFile(StoreFileRequest $request, Section $section): JsonResponse
    {
        $this->authorize('update', $section);

        $ext = $request->file('file')->getClientOriginalExtension() ?: 'bin';
        $customName = $section->getMediaFileNameForCollection('files', $ext);
        $adder = $section->addMediaFromRequest('file');
        if ($customName !== null && $customName !== '') {
            $adder->usingFileName($customName);
        }
        $media = $adder
            ->withCustomProperties([
                'title' => $request->input('title'),
                'comment' => $request->input('comment'),
                'description' => $request->input('description'),
            ])
            ->toMediaCollection('files');

        $order = $request->input('order');
        if (is_numeric($order)) {
            $media->order_column = (int) $order;
            $media->save();
        }

        $filePayload = [
            'id' => $media->id,
            'file' => $media->getUrl(),
            'url' => $media->getUrl(),
            'title' => $media->getCustomProperty('title'),
            'comment' => $media->getCustomProperty('comment'),
            'description' => $media->getCustomProperty('description'),
        ];
        if ($media->hasGeneratedConversion('thumb')) {
            $filePayload['thumb_url'] = $media->getUrl('thumb');
        }

        return response()->json(['success' => true, 'file' => $filePayload]);
    }

    /**
     * Supprime un fichier (média) lié à une section.
     *
     * @param  Media  $medium  Média à supprimer (doit appartenir à cette section)
     */
    public function deleteFile(Section $section, Media $medium): JsonResponse
    {
        $this->authorize('update', $section);

        if ((int) $medium->model_id !== (int) $section->id || $medium->collection_name !== 'files') {
            return response()->json(['success' => false, 'message' => 'Fichier non associé à cette section.'], 403);
        }

        $medium->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Associe un utilisateur à la section.
     */
    public function attachUser(Request $request, Section $section): JsonResponse
    {
        $this->authorize('update', $section);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $section->users()->attach($request->user_id);

        return response()->json(['success' => true]);
    }

    /**
     * Dissocie un utilisateur de la section.
     */
    public function detachUser(Request $request, Section $section): JsonResponse
    {
        $this->authorize('update', $section);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $section->users()->detach($request->user_id);

        return response()->json(['success' => true]);
    }

    /**
     * Synchronise la liste des utilisateurs associés à la section.
     */
    public function syncUsers(Request $request, Section $section): JsonResponse
    {
        $this->authorize('update', $section);
        $request->validate(['user_ids' => 'array', 'user_ids.*' => 'exists:users,id']);
        $section->users()->sync($request->user_ids);

        return response()->json(['success' => true]);
    }

    /**
     * Liste les utilisateurs associés à la section.
     */
    public function users(Section $section): JsonResponse
    {
        $this->authorize('view', $section);

        return response()->json($section->users);
    }

    /**
     * Restaure une section supprimée.
     *
     * @param  int  $section  ID de la section (soft-deleted)
     */
    public function restore(int $section): RedirectResponse
    {
        $model = Section::withTrashed()->findOrFail($section);
        $this->authorize('restore', $model);
        $model->restore();
        NotificationService::notifyEntityRestored($model, request()->user());

        return redirect()->route('sections.index')->with('success', 'Section restaurée.');
    }

    /**
     * Supprime définitivement une section.
     */
    public function forceDelete(Section $section): RedirectResponse
    {
        $this->authorize('forceDelete', $section);
        $section->forceDelete();
        NotificationService::notifyEntityForceDeleted($section, request()->user());

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
     * @param  Request  $request  Requête contenant le tableau de sections
     * @return JsonResponse Réponse JSON avec success: true
     *
     * @throws AuthorizationException Si l'utilisateur n'a pas les droits
     */
    public function reorder(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Section::class);

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
