<?php

namespace App\Jobs;

use App\Models\DataSubjectRequest;
use App\Models\PrivacyExport;
use App\Services\Privacy\UserDataExportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateUserDataExportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $privacyExportId,
        public ?int $dataSubjectRequestId = null
    ) {
    }

    public function handle(UserDataExportService $exportService): void
    {
        $privacyExport = PrivacyExport::query()->find($this->privacyExportId);
        if (! $privacyExport) {
            return;
        }

        $request = null;
        if ($this->dataSubjectRequestId !== null) {
            $request = DataSubjectRequest::query()->find($this->dataSubjectRequestId);
            if ($request) {
                $request->update([
                    'status' => DataSubjectRequest::STATUS_PROCESSING,
                    'confirmed_at' => $request->confirmed_at ?? now(),
                ]);
            }
        }

        try {
            $exportService->generate($privacyExport);

            if ($request) {
                $request->update([
                    'status' => DataSubjectRequest::STATUS_COMPLETED,
                    'processed_at' => now(),
                    'expires_at' => $privacyExport->fresh()?->expires_at,
                ]);
            }
        } catch (\Throwable $exception) {
            $privacyExport->update([
                'status' => PrivacyExport::STATUS_FAILED,
                'meta' => array_merge($privacyExport->meta ?? [], [
                    'error' => $exception->getMessage(),
                ]),
            ]);

            if ($request) {
                $request->update([
                    'status' => DataSubjectRequest::STATUS_FAILED,
                    'processed_at' => now(),
                    'meta' => array_merge($request->meta ?? [], [
                        'error' => $exception->getMessage(),
                    ]),
                ]);
            }

            report($exception);
        }
    }
}

