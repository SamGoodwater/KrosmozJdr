<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectBackupWebRequest;
use App\Jobs\RunProjectBackupJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Interface admin : lancement asynchrone de `project:backup`.
 */
class ProjectBackupWebController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/backup/Index');
    }

    public function store(StoreProjectBackupWebRequest $request): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isSuperAdmin()) {
            abort(403);
        }

        $options = $request->artisanOptions();

        Log::info('admin.project_backup.dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'option_keys' => array_keys($options),
        ]);

        RunProjectBackupJob::dispatch($user->id, $options);

        return redirect()
            ->route('admin.backup.index')
            ->with('success', 'Sauvegarde planifiée. Vérifiez qu’un worker traite la file (`php artisan queue:work`).');
    }
}
