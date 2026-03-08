<?php

namespace App\Console\Commands;

use App\Jobs\ExecuteUserErasureJob;
use App\Models\DataSubjectRequest;
use Illuminate\Console\Command;

/**
 * Traite les demandes de suppression RGPD dont le délai de rétractation est passé.
 *
 * Planifié quotidiennement (ex: 02:00).
 */
class ProcessPrivacyDeletionRequestsCommand extends Command
{
    protected $signature = 'privacy:process-deletion-requests';

    protected $description = 'Traite les demandes de suppression RGPD dont le délai de rétractation est écoulé';

    public function handle(): int
    {
        $requests = DataSubjectRequest::query()
            ->where('type', DataSubjectRequest::TYPE_ERASURE)
            ->where('status', DataSubjectRequest::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->with('user')
            ->get();

        $count = 0;
        foreach ($requests as $request) {
            if (! $request->user) {
                $request->update(['status' => DataSubjectRequest::STATUS_FAILED, 'processed_at' => now()]);
                continue;
            }
            ExecuteUserErasureJob::dispatch($request->id);
            $count++;
        }

        if ($count > 0) {
            $this->info("{$count} demande(s) de suppression envoyée(s) au traitement.");
        }

        return self::SUCCESS;
    }
}
