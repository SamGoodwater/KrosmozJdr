<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Characteristic;
use App\Models\CharacteristicEntity;
use App\Services\Characteristic\CharacteristicService;

/**
 * Invalide le cache des caractéristiques à chaque modification en base.
 */
class CharacteristicConfigObserver
{
    public function __construct(
        private readonly CharacteristicService $characteristicService
    ) {
    }

    public function saved(Characteristic|CharacteristicEntity $model): void
    {
        $this->characteristicService->clearCache();
    }

    public function deleted(Characteristic|CharacteristicEntity $model): void
    {
        $this->characteristicService->clearCache();
    }
}
