<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectDataSyncRequest;
use App\Jobs\RunProjectDataSyncJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Super admin : lancement asynchrone de `project:data sync` (file d’attente).
 *
 * Sécurité : `role:super_admin` ; page protégée comme le scrapping (modale mot de passe côté UI) ;
 * POST `/sync` : `password.confirm` + validation stricte (FormRequest) ;
 * job isolé ; journalisation des déclenchements.
 */
class ProjectMaintenanceController extends Controller
{
    /**
     * Formulaire + rappels (worker queue requis).
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/project-maintenance/Index', [
            'entityChoices' => StoreProjectDataSyncRequest::ENTITY_CHOICES,
            'catalogTypeChoices' => StoreProjectDataSyncRequest::CATALOG_TYPE_CHOICES,
        ]);
    }

    public function store(StoreProjectDataSyncRequest $request): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isSuperAdmin()) {
            abort(403);
        }

        $params = $request->artisanParameters();

        Log::info('admin.project_maintenance.sync_dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'option_keys' => array_keys($params),
            'has_entity_filter' => isset($params['--entity']),
            'has_catalog' => isset($params['--type']) || ($params['--races'] ?? false),
            'dry_run' => (bool) ($params['--dry-run'] ?? false),
        ]);

        RunProjectDataSyncJob::dispatch($user->id, $params);

        return redirect()
            ->route('admin.project-maintenance.index')
            ->with('success', 'Synchronisation planifiée. Assurez-vous qu’un worker traite la file (`php artisan queue:work`).');
    }
}
