<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectClearWebRequest;
use App\Jobs\RunProjectClearJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Interface admin : lancement asynchrone de `project:clear` (`--safe` / `--all`).
 */
class ProjectClearWebController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/project-clear/Index', [
            'isProduction' => app()->environment('production'),
        ]);
    }

    public function store(StoreProjectClearWebRequest $request): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $options = $request->artisanOptions();

        Log::info('admin.project_clear.dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'option_keys' => array_keys($options),
        ]);

        RunProjectClearJob::dispatch($user->id, $options);

        return redirect()
            ->route('admin.project-clear.index')
            ->with('success', 'Nettoyage planifié. Un worker doit traiter la file (`php artisan queue:work`).');
    }
}
