<?php

namespace App\Jobs;

use App\Models\DataSubjectRequest;
use App\Models\PrivacyAuditLog;
use App\Models\User;
use App\Services\Privacy\UserErasureService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job d'exécution de l'effacement RGPD d'un utilisateur.
 *
 * Appelé par ProcessPrivacyDeletionRequestsCommand après la fin du délai de rétractation.
 */
class ExecuteUserErasureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $backoff = 60;

    public function __construct(
        public int $dataSubjectRequestId
    ) {}

    public function handle(UserErasureService $service): void
    {
        $request = DataSubjectRequest::with('user')->find($this->dataSubjectRequestId);
        if (! $request || $request->type !== DataSubjectRequest::TYPE_ERASURE) {
            return;
        }
        if (! in_array($request->status, [DataSubjectRequest::STATUS_PENDING, DataSubjectRequest::STATUS_PROCESSING], true)) {
            return;
        }

        $user = $request->user;
        if (! $user) {
            $request->update(['status' => DataSubjectRequest::STATUS_FAILED, 'processed_at' => now()]);
            return;
        }

        $request->update(['status' => DataSubjectRequest::STATUS_PROCESSING]);

        try {
            $service->execute($user);
            $request->update([
                'status' => DataSubjectRequest::STATUS_COMPLETED,
                'processed_at' => now(),
            ]);
            PrivacyAuditLog::log('erasure_completed', $user->id, null, [
                'request_id' => $request->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('UserErasure failed', [
                'user_id' => $user->id,
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
            $request->update([
                'status' => DataSubjectRequest::STATUS_FAILED,
                'processed_at' => now(),
                'meta' => array_merge($request->meta ?? [], ['error' => $e->getMessage()]),
            ]);
            PrivacyAuditLog::log('erasure_failed', $user->id, null, [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
