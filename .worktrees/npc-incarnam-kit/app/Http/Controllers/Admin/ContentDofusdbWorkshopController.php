<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectDataSyncRequest;
use App\Jobs\RunProjectDataSyncJob;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Atelier DofusDB (admin) : recherche/import, maj de masse, preset auto_update.
 */
class ContentDofusdbWorkshopController extends Controller
{
    use SharesProjectConsoleJob;

    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/Content/DofusdbWorkshop/Index', array_merge([
            'entityChoices' => StoreProjectDataSyncRequest::ENTITY_CHOICES,
            'catalogTypeChoices' => StoreProjectDataSyncRequest::CATALOG_TYPE_CHOICES,
        ], $this->consoleJobProps(ProjectConsoleDomain::DATA_SYNC)));
    }

    public function sync(StoreProjectDataSyncRequest $request, ProjectConsoleJobTracker $tracker): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isAdmin()) {
            abort(403);
        }

        $params = $request->artisanParameters();
        $commandLine = ProjectConsoleJobTracker::commandLine('project:data sync', $params);
        $record = $tracker->tryQueue(ProjectConsoleDomain::DATA_SYNC, $commandLine, $user->id);
        if ($record === null) {
            return redirect()
                ->route('admin.content.dofusdb.index')
                ->with('error', ProjectConsoleDomain::busyMessage(ProjectConsoleDomain::DATA_SYNC));
        }

        Log::info('admin.content.dofusdb.sync_dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'option_keys' => array_keys($params),
            'console_job_id' => $record->id,
        ]);

        RunProjectDataSyncJob::dispatch($user->id, $params, $record->id);

        return redirect()
            ->route('admin.content.dofusdb.index')
            ->with('success', 'Synchronisation auto_update planifiée. Un worker doit traiter la file (`php artisan queue:work`).');
    }
}
