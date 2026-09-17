<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Support\KamasAmount;

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
            if (method_exists($model, 'syncCalculatedPriceFromFormula')) {
                $model->syncCalculatedPriceFromFormula();
            }
            $model->adoptLegacyDisplayedPriceIfUnset();
            $model->price = (string) $model->totalPriceKamas();
        });
    }

    /**
     * Si les deux parts sont vides, reprend la colonne `price` existante comme ajustement.
     * Évite d’écraser un barème JDR / Dofus encore stocké uniquement dans `price`.
     */
    public function adoptLegacyDisplayedPriceIfUnset(): void
    {
        if ($this->price_calculated !== null || $this->price_custom !== null) {
            return;
        }

        $existing = $this->price;
        if ($existing === null || $existing === '') {
            return;
        }

        $adopted = KamasAmount::parse($existing);
        if ($adopted <= 0) {
            return;
        }

        $this->price_custom = $adopted;
    }

    /**
     * Pose le total affiché : `price_custom` = total souhaité − part calculée (null = plus d’ajustement).
     */
    public function applyDisplayedPriceKamas(?int $desiredTotal): void
    {
        if (method_exists($this, 'syncCalculatedPriceFromFormula')) {
            $this->syncCalculatedPriceFromFormula();
        }

        if ($desiredTotal === null) {
            $this->price_custom = null;

            return;
        }

        $calc = $this->price_calculated !== null ? (int) $this->price_calculated : 0;
        $this->price_custom = $desiredTotal - $calc;
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
