<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Jobs\RunRulesCompileDownloadsJob;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Lance la compilation PDF/ODT du livre de règles depuis la gestion du contenu.
 */
class RulesDownloadsController extends Controller
{
    use SharesProjectConsoleJob;

    public function store(Request $request, ProjectConsoleJobTracker $tracker): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isAdmin()) {
            abort(403);
        }

        $commandLine = ProjectConsoleJobTracker::commandLine('rules:compile-downloads', []);
        $record = $tracker->tryQueue(ProjectConsoleDomain::RULES_DOWNLOADS, $commandLine, $user->id);
        if ($record === null) {
            return redirect()
                ->route('admin.content.dashboard.index')
                ->with('error', ProjectConsoleDomain::busyMessage(ProjectConsoleDomain::RULES_DOWNLOADS));
        }

        Log::info('admin.rules_downloads.dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'console_job_id' => $record->id,
        ]);

        RunRulesCompileDownloadsJob::dispatch($user->id, $record->id);

        return redirect()
            ->route('admin.content.dashboard.index')
            ->with('success', 'Compilation du livre de règles planifiée. Un worker doit exécuter la file.');
    }
}
