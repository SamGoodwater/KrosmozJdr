<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/auth.php';
require __DIR__ . '/user.php';
require __DIR__ . '/file.php';
require __DIR__ . '/admin/characteristics.php';
require __DIR__ . '/admin/dofus-conversion-formulas.php';

// Routes publiques
Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
})->name('home');

// STATICS
Route::get('/contribuer', function () {
    return Inertia::render('Statics/contribute');
})->name('contribute');

// PAGES ET SECTIONS
require __DIR__ . '/page.php';

// ENTITIES
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

// SERVICES
require __DIR__ . '/services/scrapping.php';