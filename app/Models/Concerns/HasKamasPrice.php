<?php

declare(strict_types=1);

namespace App\Models\Concerns;

/**
 * Prix kamas : part calculée + ajustement manuel, colonne `price` synchronisée.
 *
 * @property int|null $price_calculated
 * @property int|null $price_custom
 * @property string|null $price
 *
 * @example
 * $item->price_calculated = 1200;
 * $item->price_custom = -200;
 * $item->save(); // price = "1000"
 */
trait HasKamasPrice
{
    protected static function bootHasKamasPrice(): void
    {
        static::saving(function (self $model): void {
            $model->price = (string) $model->totalPriceKamas();
        });
    }

    /**
     * Total kamas (entier, plancher à 0) : part calculée + part personnalisée (peut être négative).
     */
    public function totalPriceKamas(): int
    {
        $calc = $this->price_calculated !== null ? (int) $this->price_calculated : 0;
        $custom = $this->price_custom !== null ? (int) $this->price_custom : 0;

        return max(0, (int) round($calc + $custom));
    }

    /**
     * Prix à exposer dans les vues lecture (null si total ≤ 0).
     */
    public function displayPriceKamas(): ?int
    {
        $total = $this->totalPriceKamas();

        return $total > 0 ? $total : null;
    }
}
