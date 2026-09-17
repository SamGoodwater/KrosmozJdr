<?php

use App\Http\Controllers\Type\ResourceTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('entities/resource-types')->name('entities.resource-types.')->middleware(['auth', 'role:admin', 'content.area'])->group(function () {
    Route::get('/', [ResourceTypeController::class, 'index'])->name('index');
    Route::get('/{resourceType}/edit', [ResourceTypeController::class, 'edit'])->name('edit');
    Route::get('/{resourceType}', [ResourceTypeController::class, 'show'])->name('show');
    Route::post('/', [ResourceTypeController::class, 'store'])->name('store');
    Route::patch('/{resourceType}', [ResourceTypeController::class, 'update'])->name('update');
    Route::delete('/{resourceType}', [ResourceTypeController::class, 'delete'])->name('delete');
});
