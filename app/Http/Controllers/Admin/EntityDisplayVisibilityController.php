<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateEntityDisplayVisibilityRequest;
use App\Models\ApplicationSetting;
use App\Models\User;
use App\Services\EntityDisplay\EntityDisplayVisibilityService;
use App\Support\EntityPermissions\EntityPermissionService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Matrice « Gérer l’affichage » : rôle minimal par état et par type d’entité (hors bypass admin).
 */
class EntityDisplayVisibilityController extends Controller
{
    public function index(EntityDisplayVisibilityService $visibilityService): InertiaResponse
    {
        return Inertia::render('Admin/EntityDisplayVisibility/Index', [
            'matrix' => $visibilityService->matrixForManageableEntities(),
            'entityKeys' => $visibilityService->manageableEntityPermissionKeys(),
            'states' => [
                ['value' => 'raw', 'label' => 'Brut'],
                ['value' => 'draft', 'label' => 'Brouillon'],
                ['value' => 'playable', 'label' => 'Jouable'],
                ['value' => 'archived', 'label' => 'Archivé'],
            ],
            'roles' => [
                ['value' => User::ROLE_GUEST, 'label' => 'Invité'],
                ['value' => User::ROLE_USER, 'label' => 'Utilisateur'],
                ['value' => User::ROLE_PLAYER, 'label' => 'Joueur'],
                ['value' => User::ROLE_GAME_MASTER, 'label' => 'Meneur de jeu'],
                ['value' => User::ROLE_ADMIN, 'label' => 'Administrateur'],
                ['value' => User::ROLE_SUPER_ADMIN, 'label' => 'Super administrateur'],
            ],
        ]);
    }

    public function update(
        UpdateEntityDisplayVisibilityRequest $request,
        EntityDisplayVisibilityService $visibilityService,
    ): RedirectResponse {
        $sanitized = $visibilityService->sanitizeStoredPayload($request->validated('rules'));

        ApplicationSetting::query()->updateOrCreate(
            ['key' => EntityDisplayVisibilityService::SETTINGS_KEY],
            ['value' => $sanitized]
        );

        $visibilityService->forgetRulesCache();
        EntityPermissionService::bumpPermissionsCacheRevision();

        return redirect()
            ->route('admin.entity-display-visibility.index')
            ->with('success', 'Règles d’affichage enregistrées.');
    }
}
