<?php

declare(strict_types=1);

/**
 * Index des guides de création destinés à l’IA de conversion (et à l’atelier MJ).
 *
 * @return array<string, array<string, mixed>>
 */
$k = require __DIR__.'/_krefs.php';

$load = static function (string $file) use ($k): array {
    /** @var callable(array<string, string>): array<string, mixed> $factory */
    $factory = require $file;

    return $factory($k);
};

return [
    'spell' => $load(__DIR__.'/spell.php'),
    'monster' => $load(__DIR__.'/monster.php'),
    'item' => $load(__DIR__.'/item.php'),
    'consumable' => $load(__DIR__.'/consumable.php'),
    'capability' => $load(__DIR__.'/capability.php'),
    'trait' => $load(__DIR__.'/trait.php'),
    'resource' => $load(__DIR__.'/resource.php'),
];
