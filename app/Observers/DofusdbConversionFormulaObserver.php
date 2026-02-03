<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\DofusdbConversionFormula;
use App\Services\Characteristic\DofusConversion\DofusDbConversionFormulaService;

/**
 * Invalide le cache des formules DofusDB à chaque modification en base.
 */
class DofusdbConversionFormulaObserver
{
    public function __construct(
        private readonly DofusDbConversionFormulaService $formulaService
    ) {
    }

    public function saved(DofusdbConversionFormula $model): void
    {
        $this->formulaService->clearCache();
    }

    public function deleted(DofusdbConversionFormula $model): void
    {
        $this->formulaService->clearCache();
    }
}
