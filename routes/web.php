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

$uniqidRegex = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
$slugRegex = '[a-z0-9]+(?:-[a-z0-9]+)*';

include_once __DIR__ . '/auth.php';

// Routes publiques
Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
})->name('home');

Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
Route::get('/pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');

// Routes authentifiées
Route::middleware(['auth', 'verified'])->group(function () {
    // Pages
    Route::prefix('pages')->name('pages.')->group(function () {
        // Création réservée aux contributeurs
        Route::middleware('role:contributor')->group(function () {
            Route::get('/create', [PageController::class, 'create'])->name('create');
            Route::post('/', [PageController::class, 'store'])->name('store');
        });

        // Modification réservée aux game_master
        Route::middleware('role:game_master')->group(function () {
            Route::get('/{page:slug}/edit', [PageController::class, 'edit'])->name('edit');
            Route::patch('/{page:uniqid}', [PageController::class, 'update'])->name('update');
            Route::delete('/{page:uniqid}', [PageController::class, 'delete'])->name('delete');
        });
    });

    // Sections
    Route::prefix('sections')->name('sections.')->group(function () {
        // Création réservée aux contributeurs
        Route::middleware('role:contributor')->group(function () {
            Route::get('/create', [SectionController::class, 'create'])->name('create');
            Route::post('/', [SectionController::class, 'store'])->name('store');
        });

        // Modification réservée aux game_master
        Route::middleware('role:game_master')->group(function () {
            Route::get('/{section:slug}/edit', [SectionController::class, 'edit'])->name('edit');
            Route::patch('/{section:uniqid}', [SectionController::class, 'update'])->name('update');
            Route::delete('/{section:uniqid}', [SectionController::class, 'delete'])->name('delete');
        });
    });
});

// Routes admin
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Pages
        Route::delete('/pages/{page:uniqid}/force', [PageController::class, 'forceDelete'])
            ->name('pages.forceDelete');
        Route::post('/pages/{page:uniqid}/restore', [PageController::class, 'restore'])
            ->name('pages.restore');

        // Sections
        Route::delete('/sections/{section:uniqid}/force', [SectionController::class, 'forceDelete'])
            ->name('sections.forceDelete');
        Route::post('/sections/{section:uniqid}/restore', [SectionController::class, 'restore'])
            ->name('sections.restore');
    });
});

// FILES
require __DIR__ . "/files.php";

// STATICS
Route::get('/contribuer', function () {
    return Inertia::render('Statics/contribute');
})->name('contribute');

// // Campaigns
// require_once __DIR__ . '/modules/campaign.php';

// // Scenarios
// require_once __DIR__ . '/modules/scenario.php';

// // Items
// require_once __DIR__ . '/modules/item.php';

// // Itemtypes
// require_once __DIR__ . '/modules/itemtype.php';

// // Attributes
// require_once __DIR__ . '/modules/attribute.php';

// // Capabilities
// require_once __DIR__ . '/modules/capability.php';

// // Classes
// require_once __DIR__ . '/modules/classe.php';

// //Conditions
// require_once __DIR__ . '/modules/condition.php';

// // Consumables
// require_once __DIR__ . '/modules/consumable.php';

// // Cosumabletypes
// require_once __DIR__ . '/modules/consumabletype.php';

// // Mobs
// require_once __DIR__ . '/modules/mob.php';

// // Mobraces
// require_once __DIR__ . '/modules/mobrace.php';

// // Npcs
// require_once __DIR__ . '/modules/npc.php';

// // Panoplies
// require_once __DIR__ . '/modules/panoply.php';

// // Resources
// require_once __DIR__ . '/modules/resource.php';

// // ResourceTypes
// require_once __DIR__ . '/modules/resourcetype.php';

// // Shops
// require_once __DIR__ . '/modules/shop.php';

// // Specializations
// require_once __DIR__ . '/modules/specialization.php';

// // Spells
// require_once __DIR__ . '/modules/spell.php';

// // Spelltypes
// require_once __DIR__ . '/modules/spelltype.php';


// // Système de gestion des images avec Glyde : https://grafikart.fr/tutoriels/image-resize-glide-php-1358
// // Impossible d'installer glyde
// // Route::get('/image/{path}', [App\Http\Controllers\Utilities\ImageController::class, 'show']);
