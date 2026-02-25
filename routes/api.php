<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Point d'entrée
|--------------------------------------------------------------------------
|
| Les routes API sont découpées par thème dans routes/api/.
| Chaque fichier est chargé ci-dessous. Préfixe "api" et middleware
| "api" appliqués par bootstrap/app.php.
|
| Voir docs/10-BestPractices/ROUTES_ARCHITECTURE.md.
|
*/

require __DIR__ . '/api/auth.php';
require __DIR__ . '/api/scrapping.php';
require __DIR__ . '/api/types.php';
require __DIR__ . '/api/entity-table.php';
require __DIR__ . '/api/tables.php';
require __DIR__ . '/api/entities.php';
