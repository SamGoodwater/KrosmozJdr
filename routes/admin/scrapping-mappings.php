<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ScrappingMappingController;
use Illuminate\Support\Facades\Route;

/**
 * Mapping scrapping (pipeline contenu) — admin+.
 */
Route::prefix('admin/content/scrapping-mappings')
    ->name('admin.scrapping-mappings.')
    ->middleware(['auth', 'role:admin', 'content.area', 'password.confirm'])
    ->group(function () {
        Route::get('/', [ScrappingMappingController::class, 'index'])->name('index');
        Route::post('/', [ScrappingMappingController::class, 'store'])->name('store');
        Route::patch('/{id}', [ScrappingMappingController::class, 'update'])->name('update');
        Route::delete('/{id}', [ScrappingMappingController::class, 'destroy'])->name('destroy');
    });
