<?php

declare(strict_types=1);

namespace App\Jobs\GenerativeAi;

use App\Services\GenerativeAi\ConversionPipeline;
use App\Services\GenerativeAi\ConversionRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Un paquet de conversion IA = une requête LLM. Queue `database`.
 *
 * @example ConvertPacketJob::dispatch($request);
 */
final class ConvertPacketJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 180;

    public function __construct(public ConversionRequest $request)
    {
        $this->onQueue('default');
    }

    public function handle(ConversionPipeline $pipeline): void
    {
        $pipeline->run($this->request);
    }
}
