<?php

/*
|--------------------------------------------------------------------------
| API Routes — Point d'entrée
|--------------------------------------------------------------------------
|
| Les routes API sont découpées par thème dans routes/api/.
| Chaque fichier est chargé ci-dessous. Préfixe "api" et middleware
| "api" appliqués par bootstrap/app.php.
|
| Voir docs/backend/routes/README.md.
|
*/

require __DIR__.'/api/auth.php';
require __DIR__.'/api/characteristics.php';
require __DIR__.'/api/scrapping.php';
require __DIR__.'/api/types.php';
require __DIR__.'/api/entity-table.php';
require __DIR__.'/api/tables.php';
require __DIR__.'/api/table-presets.php';
require __DIR__.'/api/entities.php';
require __DIR__.'/api/effects.php';
require __DIR__.'/api/global-search.php';
require __DIR__.'/api/object-effects.php';
require __DIR__.'/api/cms.php';
