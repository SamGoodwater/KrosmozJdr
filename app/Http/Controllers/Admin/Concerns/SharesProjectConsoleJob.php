<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\ProjectConsoleJob;

/**
 * Props Inertia du dernier job console d’un domaine.
 */
trait SharesProjectConsoleJob
{
    /**
     * @return array{consoleJob: array<string, mixed>|null}
     */
    protected function consoleJobProps(string $domain): array
    {
        $job = ProjectConsoleJob::latestForDomain($domain);

        return [
            'consoleJob' => $job?->toStatusPayload(),
        ];
    }
}
