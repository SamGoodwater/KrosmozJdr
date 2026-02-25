<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web — Pages et sections (contenu éditable)
|--------------------------------------------------------------------------
*/

Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('index');
    Route::get('/menu', [PageController::class, 'menu'])->name('menu');
    Route::get('/{page:slug}', [PageController::class, 'show'])->name('show')->where('page', '[a-z0-9]+(?:-[a-z0-9]+)*');

    Route::middleware('auth')->group(function () {
        Route::patch('/reorder', [PageController::class, 'reorder'])->name('reorder');
        Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
        Route::patch('/{page}', [PageController::class, 'update'])->name('update');
        Route::get('/create', [PageController::class, 'create'])->name('create');
        Route::post('/', [PageController::class, 'store'])->name('store');
        Route::delete('/{page}', [PageController::class, 'delete'])->name('delete');
        Route::post('/{page}/restore', [PageController::class, 'restore'])->name('restore');
        Route::delete('/{page}/force', [PageController::class, 'forceDelete'])->name('forceDelete');
    });
});

Route::prefix('sections')->name('sections.')->middleware('auth')->group(function () {
    Route::get('/', [SectionController::class, 'index'])->name('index');
    Route::patch('/reorder', [SectionController::class, 'reorder'])->name('reorder');
    Route::get('/create', [SectionController::class, 'create'])->name('create');
    Route::post('/', [SectionController::class, 'store'])->name('store');
    Route::get('/{section}', [SectionController::class, 'show'])->name('show')->where('section', '[a-z0-9]+(?:-[a-z0-9]+)*');
    Route::get('/{section}/edit', [SectionController::class, 'edit'])->name('edit');
    Route::patch('/{section}', [SectionController::class, 'update'])->name('update');
    Route::delete('/{section}', [SectionController::class, 'delete'])->name('delete');
    Route::post('/{section}/restore', [SectionController::class, 'restore'])->name('restore');
    Route::delete('/{section}/force', [SectionController::class, 'forceDelete'])->name('forceDelete');
    Route::post('/{section}/files', [SectionController::class, 'storeFile'])->name('files.store');
    Route::delete('/{section}/files/{medium}', [SectionController::class, 'deleteFile'])->name('files.delete');
});
