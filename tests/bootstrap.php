<?php

/**
 * Bootstrap PHPUnit : charge Composer uniquement.
 * APP_ENV, DB_* : définis dans phpunit.xml (section php/env) ou .env.testing.
 */

// Évite qu'un cache Laravel stale masque la configuration réelle des tests.
@unlink(dirname(__DIR__).'/bootstrap/cache/config.php');

// Évite que `php artisan route:cache` (fichier stale) masque les routes réelles pendant les tests.
foreach (glob(dirname(__DIR__).'/bootstrap/cache/routes-*.php') ?: [] as $routeCacheFile) {
    @unlink($routeCacheFile);
}

require __DIR__.'/../vendor/autoload.php';
