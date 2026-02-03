<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\EntityCharacteristic;
use App\Services\Characteristic\CharacteristicService;

/**
 * Invalide le cache des caractéristiques à chaque modification entity_characteristics.
 */
class CharacteristicConfigObserver
{
    public function __construct(
        private readonly CharacteristicService $characteristicService
    ) {
    }

    public function saved(EntityCharacteristic $model): void
    {
        $this->characteristicService->clearCache();
    }

    public function deleted(EntityCharacteristic $model): void
    {
        $this->characteristicService->clearCache();
    }
}
