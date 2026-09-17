<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectOrphanFilesWebRequest;
use App\Models\MediaCleanupJob;
use App\Services\Media\MediaCleanupDispatcher;
use App\Services\Media\OrphanPublicMediaCleanupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use RuntimeException;

/**
 * Interface super-admin : nettoyage suivi des fichiers MediaLibrary orphelins.
 */
class ProjectOrphanFilesWebController extends Controller
{
    public function __construct(
        private readonly MediaCleanupDispatcher $dispatcher,
        private readonly OrphanPublicMediaCleanupService $cleanupService,
    ) {}

    public function index(): InertiaResponse
    {
        $recent = MediaCleanupJob::query()
            ->orderByDesc('created_at')
            ->limit(15)
            ->get()
            ->map(fn (MediaCleanupJob $job) => $job->toStatusPayload())
            ->values()
            ->all();

        $active = MediaCleanupJob::query()
            ->whereIn('status', [MediaCleanupJob::STATUS_QUEUED, MediaCleanupJob::STATUS_RUNNING])
            ->orderByDesc('created_at')
            ->first();

        return Inertia::render('Admin/orphan-files/Index', [
            'scannedRoots' => $this->cleanupService->scannedRoots(),
            'recentJobs' => $recent,
            'activeJob' => $active?->toStatusPayload(),
        ]);
    }

    public function store(StoreProjectOrphanFilesWebRequest $request): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $delete = $request->boolean('delete');
        $skipNotify = $request->boolean('skip_notify');
        $mode = $delete ? MediaCleanupJob::MODE_DELETE : MediaCleanupJob::MODE_DRY_RUN;

        Log::info('admin.project_orphan_files.dispatched', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'mode' => $mode,
            'skip_notify' => $skipNotify,
        ]);

        try {
            $job = $this->dispatcher->dispatch($mode, $user->id, [], $skipNotify);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages([
                'delete' => $e->getMessage(),
            ]);
        }

        $label = $delete ? 'suppression' : 'dry-run';

        return redirect()
            ->route('admin.orphan-files.index')
            ->with('success', "Nettoyage orphelin planifié ({$label}, job {$job->id}). Un worker doit traiter la file (`php artisan queue:work`).");
    }

    public function status(Request $request, string $jobId): JsonResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $job = MediaCleanupJob::query()->find($jobId);
        if (! $job) {
            return response()->json([
                'success' => false,
                'message' => 'Job introuvable',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $job->toStatusPayload(),
        ]);
    }

    public function cancel(Request $request, string $jobId): JsonResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $job = MediaCleanupJob::query()->find($jobId);
        if (! $job) {
            return response()->json([
                'success' => false,
                'message' => 'Job introuvable',
            ], 404);
        }

        if ($job->isTerminal()) {
            return response()->json([
                'success' => true,
                'message' => 'Le job est déjà terminé.',
                'data' => $job->toStatusPayload(),
            ]);
        }

        $job->status = MediaCleanupJob::STATUS_CANCELLED;
        $job->cancelled_at = now();
        $job->finished_at = now();
        $job->save();

        Log::info('admin.project_orphan_files.cancelled', [
            'user_id' => $user->id,
            'job_id' => $job->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Annulation demandée. Le worker s’arrêtera entre deux fichiers.',
            'data' => $job->toStatusPayload(),
        ]);
    }
}
