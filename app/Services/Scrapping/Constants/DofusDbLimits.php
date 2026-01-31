<?php

namespace App\Services\Scrapping\Constants;

/**
 * Constantes liées aux limites connues de DofusDB.
 */
final class DofusDbLimits
{
    /**
     * DofusDB cappe fréquemment les pages à 50, même si on demande plus.
     */
    public const PAGE_LIMIT = 50;
}

