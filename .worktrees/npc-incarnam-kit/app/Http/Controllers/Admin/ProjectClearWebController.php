<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectClearWebRequest;
use App\Jobs\RunProjectClearJob;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Interface admin : lancement asynchrone de `project:clear` (`--safe` / `--all`).
 */
class ProjectClearWebController extends Controller
{
    use SharesProjectConsoleJob;

    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/project-clear/Index', array_merge([
            'isProduction' => app()->environment('production'),
        ], $this->consoleJobProps(ProjectConsoleDomain::CLEAR)));
    }

    public function store(StoreProjectClearWebRequest $request, ProjectConsoleJobTracker $tracker): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $options = $request->artisanOptions();
        $commandLine = ProjectConsoleJobTracker::commandLine('project:clear', $options);
        $record = $tracker->tryQueue(ProjectConsoleDomain::CLEAR, $commandLine, $user->id);
        if ($record === null) {
            return redirect()
                ->route('admin.project-clear.index')
                ->with('error', ProjectConsoleDomain::busyMessage(ProjectConsoleDomain::CLEAR));
        }

        Log::info('admin.project_clear.dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'option_keys' => array_keys($options),
            'console_job_id' => $record->id,
        ]);

        RunProjectClearJob::dispatch($user->id, $options, $record->id);

        return redirect()
            ->route('admin.project-clear.index')
            ->with('success', 'Nettoyage planifié. Un worker doit traiter la file (`php artisan queue:work`).');
    }
}
