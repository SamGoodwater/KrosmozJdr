<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SectionController;

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
use App\Http\Controllers\Modules\ResourceController;
use App\Http\Controllers\Modules\ShopController;
use App\Http\Controllers\Modules\SpecializationController;
use App\Http\Controllers\Modules\SpellController;
use App\Http\Controllers\Modules\SpelltypeController;
use App\Http\Controllers\Modules\ItemtypeController;
use App\Http\Controllers\Modules\ConsumabletypeController;
use App\Http\Controllers\Modules\ResourcetypeController;
use App\Models\Section;

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
})->name('home');

// AUTH
require __DIR__ . '/auth.php';

// PERMANENT
Route::get('/contribuer', function () {
    return Inertia::render('Permanent/contribute');
})->name('contribute');

// Pages
Route::prefix('page')->name("page.")->group(function () use ($slugRegex, $uniqidRegex) {
    Route::get('/', [PageController::class, 'index'])->name('index');
    Route::get('/{page:slug}', [PageController::class, 'show'])->name('show')->where('page', $slugRegex);
    Route::get('/create', [PageController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/store', [PageController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{page:slug}/edit', [PageController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('page', $slugRegex);
    Route::patch('/{page:uniqid}', [PageController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
    Route::delete('/{page:uniqid}', [PageController::class, 'delete'])->name('delete')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
    Route::post('/{page:uniqid}', [PageController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
    Route::delete('/forcedDelete/{page:uniqid}', [PageController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('page', $uniqidRegex);
});

// Sections
Route::prefix('section')->name("section.")->group(function () use ($slugRegex, $uniqidRegex) {
    Route::get('/', [SectionController::class, 'index'])->name('index');
    Route::get('/{section:slug}', [SectionController::class, 'show'])->name('show')->where('section', $slugRegex);
    Route::get('/create', [SectionController::class, 'create'])->name('create')->middleware(['auth', 'verified']);
    Route::post('/store', [SectionController::class, 'store'])->name('store')->middleware(['auth', 'verified']);
    Route::get('/{section:slug}/edit', [SectionController::class, 'edit'])->name('edit')->middleware(['auth', 'verified'])->where('section', $slugRegex);
    Route::patch('/{section:uniqid}', [SectionController::class, 'update'])->name('update')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::delete('/{section:uniqid}', [SectionController::class, 'delete'])->name('destroy')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::post('/{section:uniqid}', [SectionController::class, 'restore'])->name('restore')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
    Route::delete('/forcedDelete/{section:uniqid}', [SectionController::class, 'forcedDelete'])->name('forcedDelete')->middleware(['auth', 'verified'])->where('section', $uniqidRegex);
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

// Resources
require_once __DIR__ . '/modules/resource.php';

// ResourceTypes
require_once __DIR__ . '/modules/resourcetype.php';

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
