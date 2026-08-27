<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectDepsWebRequest;
use App\Jobs\RunProjectDepsJob;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Interface admin : lancement asynchrone de `project:deps` (hors production).
 */
class ProjectDepsWebController extends Controller
{
    use SharesProjectConsoleJob;

    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/project-update/Index', array_merge([
            'isProduction' => app()->environment('production'),
        ], $this->consoleJobProps(ProjectConsoleDomain::DEPS)));
    }

    public function store(StoreProjectDepsWebRequest $request, ProjectConsoleJobTracker $tracker): RedirectResponse
    {
        if (app()->environment('production')) {
            return redirect()
                ->route('admin.project-update.index')
                ->with('error', 'La mise à jour de la stack depuis l’interface n’est pas disponible en production.');
        }

        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $options = $request->artisanOptions();
        if ($options === []) {
            return redirect()
                ->route('admin.project-update.index')
                ->with('error', 'Choisissez « Tout (défaut) » ou au moins une cible (apt, composer, …).');
        }

        $commandLine = ProjectConsoleJobTracker::commandLine('project:deps', $options);
        $record = $tracker->tryQueue(ProjectConsoleDomain::DEPS, $commandLine, $user->id);
        if ($record === null) {
            return redirect()
                ->route('admin.project-update.index')
                ->with('error', ProjectConsoleDomain::busyMessage(ProjectConsoleDomain::DEPS));
        }

        Log::info('admin.project_deps.dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'option_keys' => array_keys($options),
            'console_job_id' => $record->id,
        ]);

        RunProjectDepsJob::dispatch($user->id, $options, $record->id);

        return redirect()
            ->route('admin.project-update.index')
            ->with('success', 'Mise à jour de la stack planifiée. Un worker doit exécuter la file (`php artisan queue:work`).');
    }
}
