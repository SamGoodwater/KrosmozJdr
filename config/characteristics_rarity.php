<?php

declare(strict_types=1);

/**
 * Rareté par niveau (scrapping DofusDB).
 * Utilisé par FormatterApplicator pour déduire la rareté quand elle est absente.
 * Niveau (min) => indice de rareté (0 = commun, 4 = mythique).
 *
 * @return array<int, int>
 */
return [
    'rarity_default_by_level' => [
        0 => 0,
        3 => 1,
        7 => 2,
        10 => 3,
        17 => 4,
    ],
];
