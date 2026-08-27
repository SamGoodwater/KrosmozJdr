<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectBackupWebRequest;
use App\Jobs\RunProjectBackupJob;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Interface admin : lancement asynchrone de `project:backup`.
 */
class ProjectBackupWebController extends Controller
{
    use SharesProjectConsoleJob;

    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/backup/Index', $this->consoleJobProps(ProjectConsoleDomain::BACKUP));
    }

    public function store(StoreProjectBackupWebRequest $request, ProjectConsoleJobTracker $tracker): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $options = $request->artisanOptions();
        $commandLine = ProjectConsoleJobTracker::commandLine('project:backup', $options);
        $record = $tracker->tryQueue(ProjectConsoleDomain::BACKUP, $commandLine, $user->id);
        if ($record === null) {
            return redirect()
                ->route('admin.backup.index')
                ->with('error', ProjectConsoleDomain::busyMessage(ProjectConsoleDomain::BACKUP));
        }

        Log::info('admin.project_backup.dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'option_keys' => array_keys($options),
            'console_job_id' => $record->id,
        ]);

        RunProjectBackupJob::dispatch($user->id, $options, $record->id);

        return redirect()
            ->route('admin.backup.index')
            ->with('success', 'Sauvegarde planifiée. Vérifiez qu’un worker traite la file (`php artisan queue:work`).');
    }
}
