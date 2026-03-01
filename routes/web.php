<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Point d'entrée
|--------------------------------------------------------------------------
|
| Découpage par thème : auth (racine), web/*, admin/*, entities/*, services/*.
| Voir docs/10-BestPractices/ROUTES_ARCHITECTURE.md.
|
*/

require __DIR__ . '/auth.php';
require __DIR__ . '/web/statics.php';
require __DIR__ . '/web/notifications.php';
require __DIR__ . '/web/user.php';
require __DIR__ . '/web/file.php';
require __DIR__ . '/web/page.php';

require __DIR__ . '/admin/characteristics.php';
require __DIR__ . '/admin/dofus-conversion-formulas.php';
require __DIR__ . '/admin/scrapping-mappings.php';
require __DIR__ . '/admin/dofusdb-effect-mappings.php';
require __DIR__ . '/admin/effects.php';

require __DIR__ . '/entities/attribute.php';
require __DIR__ . '/entities/campaign.php';
require __DIR__ . '/entities/capability.php';
require __DIR__ . '/entities/breed.php';
require __DIR__ . '/entities/consumable.php';
require __DIR__ . '/entities/creature.php';
require __DIR__ . '/entities/item.php';
require __DIR__ . '/entities/item-type.php';
require __DIR__ . '/entities/monster.php';
require __DIR__ . '/entities/monster-race.php';
require __DIR__ . '/entities/npc.php';
require __DIR__ . '/entities/panoply.php';
require __DIR__ . '/entities/resource.php';
require __DIR__ . '/entities/resource-type.php';
require __DIR__ . '/entities/scenario.php';
require __DIR__ . '/entities/shop.php';
require __DIR__ . '/entities/specialization.php';
require __DIR__ . '/entities/spell.php';
require __DIR__ . '/entities/spell-type.php';
require __DIR__ . '/entities/consumable-type.php';

require __DIR__ . '/services/scrapping.php';
