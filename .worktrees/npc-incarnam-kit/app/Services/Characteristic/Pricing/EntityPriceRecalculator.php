<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Pricing;

use App\Models\CharacteristicObject;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use Illuminate\Support\Facades\Log;

/**
 * Recalcule et persiste le prix automatique d’un équipement ou d’un consommable.
 *
 * @example
 * $recalculator->recalculateItem($item, resetCustom: true);
 */
final class EntityPriceRecalculator
{
    /** @var array<string, float>|null */
    private ?array $objectPriceUnitsCache = null;

    public function __construct(
        private readonly EquipmentPriceCalculator $equipmentPriceCalculator,
        private readonly ConsumablePriceCalculator $consumablePriceCalculator,
    ) {}

    /**
     * @return array<string, float>
     */
    public function objectPriceUnits(): array
    {
        if ($this->objectPriceUnitsCache !== null) {
            return $this->objectPriceUnitsCache;
        }

        $rows = CharacteristicObject::query()
            ->with('characteristic:id,key')
            ->whereNotNull('base_price_per_unit')
            ->get(['id', 'characteristic_id', 'entity', 'base_price_per_unit']);

        $units = [];
        foreach ($rows as $row) {
            $key = $row->characteristic?->key;
            if (! is_string($key) || $key === '') {
                continue;
            }
            $price = (float) $row->base_price_per_unit;
            $this->assignUnit($units, $key, $price, $row->entity === CharacteristicObject::ENTITY_ITEM);
        }

        $this->objectPriceUnitsCache = $units;

        return $units;
    }

    /**
     * Calcule le prix automatique sans persister.
     */
    public function computeItem(Item $item): int
    {
        return $this->equipmentPriceCalculator->calculate(
            $this->decodeBonus($item->bonus),
            $this->objectPriceUnits(),
            $this->parseLevel($item->level),
            max(0, (int) ($item->rarity ?? 0)),
        );
    }

    /**
     * Calcule le prix automatique sans persister.
     */
    public function computeConsumable(Consumable $consumable): int
    {
        if (! $consumable->relationLoaded('resources')) {
            $consumable->load('resources');
        }

        return $this->consumablePriceCalculator->calculateFromResources($consumable->resources);
    }

    /**
     * @param  bool  $resetCustom  Si vrai, efface l’ajustement manuel (la formule devient le total).
     */
    public function recalculateItem(Item $item, bool $resetCustom = true): Item
    {
        $item->price_calculated = $this->computeItem($item);
        if ($resetCustom) {
            $item->price_custom = null;
        }
        $item->save();

        return $item;
    }

    /**
     * @param  bool  $resetCustom  Si vrai, efface l’ajustement manuel (la formule devient le total).
     */
    public function recalculateConsumable(Consumable $consumable, bool $resetCustom = true): Consumable
    {
        $consumable->price_calculated = $this->computeConsumable($consumable);
        if ($resetCustom) {
            $consumable->price_custom = null;
        }
        $consumable->save();

        return $consumable;
    }

    /**
     * Un consommable jouable garde son barème JDR (recette ou `price_custom` sans recette).
     */
    public function shouldSkipPlayableConsumable(Consumable $consumable): bool
    {
        return $consumable->state === Consumable::STATE_PLAYABLE;
    }

    /**
     * @return int Nombre d’équipements mis à jour
     */
    public function recalculateAllItems(?callable $onProgress = null): int
    {
        $count = 0;
        $total = Item::query()->count();
        Item::query()->orderBy('id')->chunkById(100, function ($items) use (&$count, $total, $onProgress): void {
            foreach ($items as $item) {
                $this->recalculateItem($item, true);
                $count++;
            }
            if ($onProgress !== null && $total > 0) {
                $onProgress($count, $total);
            }
        });

        Log::info('EntityPriceRecalculator: items', ['updated' => $count]);

        return $count;
    }

    /**
     * @return int Nombre de consommables mis à jour
     */
    public function recalculateAllConsumables(?callable $onProgress = null): int
    {
        $count = 0;
        $query = Consumable::query()->where('state', '!=', Consumable::STATE_PLAYABLE);
        $total = (clone $query)->count();
        $query->with('resources')->orderBy('id')->chunkById(100, function ($consumables) use (&$count, $total, $onProgress): void {
            foreach ($consumables as $consumable) {
                $this->recalculateConsumable($consumable, true);
                $count++;
            }
            if ($onProgress !== null && $total > 0) {
                $onProgress($count, $total);
            }
        });

        Log::info('EntityPriceRecalculator: consumables', ['updated' => $count]);

        return $count;
    }

    /**
     * @param  array<string, float>  $units
     */
    private function assignUnit(array &$units, string $key, float $price, bool $prefer): void
    {
        $short = str_ends_with($key, '_object') ? substr($key, 0, -7) : $key;
        if ($prefer || ! array_key_exists($key, $units)) {
            $units[$key] = $price;
        }
        if ($short !== $key && ($prefer || ! array_key_exists($short, $units))) {
            $units[$short] = $price;
        }
    }

    /**
     * @return array<string, int|float>
     */
    public function decodeBonus(mixed $value): array
    {
        $decoded = is_string($value) ? json_decode($value, true) : $value;
        if (! is_array($decoded)) {
            return [];
        }

        $bonus = [];
        foreach ($decoded as $key => $rawValue) {
            if (is_numeric($rawValue)) {
                $bonus[(string) $key] = (float) $rawValue;
            }
        }

        return $bonus;
    }

    private function parseLevel(mixed $level): int
    {
        if ($level === null || $level === '') {
            return 0;
        }
        if (! is_numeric($level)) {
            return 0;
        }

        return max(0, (int) $level);
    }
}
