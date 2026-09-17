<?php

/**
 * Bootstrap PHPUnit : charge Composer uniquement.
 * APP_ENV, DB_* : définis dans phpunit.xml (section php/env) ou .env.testing.
 */

// Forcer que getenv() / $_SERVER voient les variables déjà injectées par PHPUnit
// avant le chargement du .env, afin de ne jamais exécuter les tests sur DB_DATABASE de .env par erreur.
$dbEnvKeys = [
    'APP_ENV',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD',
    'DB_CHARSET',
    'DB_COLLATION',
];
foreach ($dbEnvKeys as $key) {
    if (array_key_exists($key, $_ENV)) {
        $value = (string) $_ENV[$key];
        putenv("{$key}={$value}");
        $_SERVER[$key] = $value;
    }
}

// Évite qu'un cache Laravel stale masque la configuration réelle des tests.
@unlink(dirname(__DIR__).'/bootstrap/cache/config.php');

// Évite que `php artisan route:cache` (fichier stale) masque les routes réelles pendant les tests.
foreach (glob(dirname(__DIR__).'/bootstrap/cache/routes-*.php') ?: [] as $routeCacheFile) {
    @unlink($routeCacheFile);
}

require __DIR__.'/../vendor/autoload.php';
