<?php

use App\Http\Controllers\Modules\ItemController;
use App\Http\Controllers\Modules\CampaignController;
use App\Http\Controllers\Modules\ScenarioController;
use App\Http\Controllers\Modules\AttributeController;
use App\Http\Controllers\Modules\CapabilityController;
use App\Http\Controllers\Modules\ClasseController;
use App\Http\Controllers\Modules\ConditionController;
use App\Http\Controllers\Modules\ConsumableController;
use App\Http\Controllers\Modules\MobController;
use App\Http\Controllers\Modules\MobraceController;
use App\Http\Controllers\Modules\NpcController;
use App\Http\Controllers\Modules\PanoplyController;
use App\Http\Controllers\Modules\RessourceController;
use App\Http\Controllers\Modules\ShopController;
use App\Http\Controllers\Modules\SpecializationController;
use App\Http\Controllers\Modules\SpellController;
use App\Http\Controllers\Modules\SpelltypeController;
use App\Http\Controllers\Modules\ItemtypeController;
use App\Http\Controllers\Modules\ConsumabletypeController;
use App\Http\Controllers\Modules\RessourcetypeController;

use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

$uniqidRegex = '[A-Za-z0-9]+';
$slugRegex = '[A-Za-z0-9]+(?:(-|_).[A-Za-z0-9]+)*';

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Auth
require_once __DIR__ . '/auth.php';

// Users
Route::prefix('user')->name("user.")->controller(UserController::class)->middleware(['auth', 'verified'])->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{user:uniqid}', 'show')->name('show')->where('user', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::inertia('/{user:uniqid}/edit', 'edit')->name('edit')->where('user', $uniqidRegex);
    Route::patch('/{user:uniqid}', 'update')->name('update')->where('user', $uniqidRegex);
    Route::delete('/{user:uniqid}', 'delete')->name('delete')->where('user', $uniqidRegex);
    Route::post('/{user:uniqid}', 'restore')->name('restore')->where('user', $uniqidRegex);
    Route::delete('/{user:uniqid}', 'forcedDelete')->name('forcedDelete')->where('user', $uniqidRegex);
});

// Pages
Route::prefix('page')->name("page.")->controller(PageController::class)->group(function () use ($slugRegex, $uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{page:slug}', 'show')->name('show')->where('page', $slugRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{page:slug}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('page', $slugRegex);
    Route::patch('/{page:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
    Route::delete('/{page:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
    Route::post('/{page:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
    Route::delete('/{page:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
});

// Sections
Route::prefix("section")->name("section.")->controller(SectionController::class)->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{section:uniqid}', 'show')->name('show')->where('section', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create')->middleware(['auth', 'verified']);
    Route::post('/', 'store')->name('store')->middleware(['auth', 'verified']);
    Route::inertia('/{section:uniqid}/edit', 'edit')->name('edit')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::patch('/{section:uniqid}', 'update')->name('update')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::delete('/{section:uniqid}', 'delete')->name('delete')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::post('/{section:uniqid}', 'restore')->name('restore')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::delete('/{section:uniqid}', 'forcedDelete')->name('forcedDelete')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
});


// Campaigns
require_once __DIR__ . '/modules/campaign.php';

// Scenarios
require_once __DIR__ . '/modules/scenario.php';

// Items
require_once __DIR__ . '/modules/item.php';

// Itemtypes
require_once __DIR__ . '/modules/itemtype.php';

// Attributes
require_once __DIR__ . '/modules/attribute.php';

// Capabilities
require_once __DIR__ . '/modules/capability.php';

// Classes
require_once __DIR__ . '/modules/classe.php';

//Conditions
require_once __DIR__ . '/modules/condition.php';

// Consumables
require_once __DIR__ . '/modules/consumable.php';

// Cosumabletypes
require_once __DIR__ . '/modules/consumabletype.php';

// Mobs
require_once __DIR__ . '/modules/mob.php';

// Mobraces
require_once __DIR__ . '/modules/mobrace.php';

// Npcs
require_once __DIR__ . '/modules/npc.php';

// Panoplies
require_once __DIR__ . '/modules/panoply.php';

// Ressources
require_once __DIR__ . '/modules/ressource.php';

// RessourceTypes
require_once __DIR__ . '/modules/ressourcetype.php';

// Shops
require_once __DIR__ . '/modules/shop.php';

// Specializations
require_once __DIR__ . '/modules/specialization.php';

// Spells
require_once __DIR__ . '/modules/spell.php';

// Spelltypes
require_once __DIR__ . '/modules/spelltype.php';



// Système de gestion des images avec Glyde : https://grafikart.fr/tutoriels/image-resize-glide-php-1358
// Impossible d'installer glyde
// Route::get('/image/{path}', [App\Http\Controllers\Utilities\ImageController::class, 'show']);
