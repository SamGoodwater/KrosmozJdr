<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SectionController;

const SLUG_REGEX = '[a-z0-9]+(?:-[a-z0-9]+)*';

// --- PAGES ---

Route::prefix('pages')->name('pages.')->group(function () {
    // Lecture publique (slug pour SEO)
    Route::get('/', [PageController::class, 'index'])->name('index');
    Route::get('/{page:slug}', [PageController::class, 'show'])->name('show')->where('page', SLUG_REGEX);

    // Authentifié uniquement (la policy gère les droits fins)
    Route::middleware('auth')->group(function () {
        // Edition, update, suppression, création : droits gérés par policy (plus de middleware 'role')
        Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
        Route::patch('/{page}', [PageController::class, 'update'])->name('update');
        Route::get('/create', [PageController::class, 'create'])->name('create');
        Route::post('/', [PageController::class, 'store'])->name('store');
        Route::delete('/{page}', [PageController::class, 'destroy'])->name('delete');
        Route::post('/{page}/restore', [PageController::class, 'restore'])->name('restore');

        // Suppression (admin, super_admin)
        Route::middleware('role:admin')->group(function () {
            Route::delete('/{page}/force', [PageController::class, 'forceDelete'])->name('forceDelete');
        });
    });
});

// --- SECTIONS ---

Route::prefix('sections')->name('sections.')->middleware('auth')->group(function () {
    // Lecture (section précise via id numérique)
    Route::get('/{section}', [SectionController::class, 'show'])->name('show');

    // Edition, update, suppression, création : droits gérés par policy (plus de middleware 'role')
    Route::get('/{section}/edit', [SectionController::class, 'edit'])->name('edit');
    Route::patch('/{section}', [SectionController::class, 'update'])->name('update');
    Route::get('/create', [SectionController::class, 'create'])->name('create');
    Route::post('/', [SectionController::class, 'store'])->name('store');
    Route::delete('/{section}', [SectionController::class, 'destroy'])->name('delete');
    Route::post('/{section}/restore', [SectionController::class, 'restore'])->name('restore');

    // Suppression (admin, super_admin)
    Route::middleware('role:admin')->group(function () {
        Route::delete('/{section}/force', [SectionController::class, 'forceDelete'])->name('forceDelete');
    });

    // Fichiers liés à une section (toujours auth, policy gère le droit)
    Route::post('/{section}/files', [SectionController::class, 'storeFile'])->name('files.store');
    Route::delete('/{section}/files/{file}', [SectionController::class, 'destroyFile'])->name('files.destroy');
});
// Les droits d'accès fins sont désormais gérés uniquement par les policies (plus de middleware 'role').
