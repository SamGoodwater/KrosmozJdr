<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectConsoleJob;
use App\Models\User;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Statut JSON et annulation d’un job console admin (poll depuis les pages thématiques).
 */
class ProjectConsoleJobStatusController extends Controller
{
    public function show(Request $request, string $job): JsonResponse
    {
        $record = ProjectConsoleJob::query()->find($job);
        if ($record === null) {
            return response()->json([
                'success' => false,
                'message' => 'Job introuvable',
            ], 404);
        }

        $this->authorizeConsoleJob($request, $record);

        return response()->json([
            'success' => true,
            'data' => $record->toStatusPayload(),
        ]);
    }

    public function cancel(Request $request, string $job, ProjectConsoleJobTracker $tracker): JsonResponse
    {
        $record = ProjectConsoleJob::query()->find($job);
        if ($record === null) {
            return response()->json([
                'success' => false,
                'message' => 'Job introuvable',
            ], 404);
        }

        $this->authorizeConsoleJob($request, $record);

        if (! $tracker->cancel($record)) {
            return response()->json([
                'success' => false,
                'message' => 'Ce job n’est plus annulable.',
                'data' => $record->fresh()?->toStatusPayload(),
            ], 409);
        }

        return response()->json([
            'success' => true,
            'data' => $record->fresh()?->toStatusPayload(),
        ]);
    }

    /**
     * Super-admin : tous les domaines. Admin : sync DofusDB et compilation des règles.
     */
    private function authorizeConsoleJob(Request $request, ProjectConsoleJob $record): void
    {
        $user = $request->user();
        if ($user === null || ! $user instanceof User) {
            abort(403);
        }
        if ($user->isInteractiveSuperAdmin()) {
            return;
        }
        if ($user->isAdmin() && in_array($record->domain, [
            ProjectConsoleDomain::DATA_SYNC,
            ProjectConsoleDomain::RULES_DOWNLOADS,
        ], true)) {
            return;
        }

        abort(403);
    }
}
