<?php

namespace App\Jobs;

use App\Models\PrivacyExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredPrivacyExportsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        PrivacyExport::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->whereIn('status', [PrivacyExport::STATUS_READY, PrivacyExport::STATUS_FAILED, PrivacyExport::STATUS_EXPIRED])
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $export) {
                    if (is_string($export->path) && $export->path !== '' && Storage::disk('local')->exists($export->path)) {
                        Storage::disk('local')->delete($export->path);
                    }

                    $export->update([
                        'status' => PrivacyExport::STATUS_EXPIRED,
                    ]);
                }
            });
    }
}

