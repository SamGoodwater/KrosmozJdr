<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectConsoleJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Statut JSON d’un job console admin (poll depuis les pages thématiques).
 */
class ProjectConsoleJobStatusController extends Controller
{
    public function show(Request $request, string $job): JsonResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isInteractiveSuperAdmin()) {
            abort(403);
        }

        $record = ProjectConsoleJob::query()->find($job);
        if ($record === null) {
            return response()->json([
                'success' => false,
                'message' => 'Job introuvable',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $record->toStatusPayload(),
        ]);
    }
}
