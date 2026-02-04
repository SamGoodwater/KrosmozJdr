<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\EquipmentSlot;
use App\Models\EquipmentSlotCharacteristic;
use App\Services\Characteristic\Equipment\EquipmentCharacteristicService;

/**
 * Invalide le cache des slots d'équipement à chaque modification en base.
 */
class EquipmentCharacteristicConfigObserver
{
    public function __construct(
        private readonly EquipmentCharacteristicService $equipmentCharacteristicService
    ) {
    }

    public function saved(EquipmentSlot|EquipmentSlotCharacteristic $model): void
    {
        $this->equipmentCharacteristicService->clearCache();
    }

    public function deleted(EquipmentSlot|EquipmentSlotCharacteristic $model): void
    {
        $this->equipmentCharacteristicService->clearCache();
    }
}
