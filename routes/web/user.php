<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web — Utilisateur (profil, gestion admin)
|--------------------------------------------------------------------------
*/

Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    Route::get('/', [UserController::class, 'show'])->name('show');
    Route::get('/edit', [UserController::class, 'edit'])->name('edit');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::patch('/', [UserController::class, 'update'])->name('update');
    Route::patch('/password', [UserController::class, 'updatePassword'])->name('updatePassword');
    Route::post('/avatar', [UserController::class, 'updateAvatar'])->name('updateAvatar');
    Route::delete('/avatar', [UserController::class, 'deleteAvatar'])->name('deleteAvatar');
    Route::delete('/', [UserController::class, 'delete'])->name('delete');

    Route::middleware('role:admin')->group(function () {
        Route::get('/list', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('admin.edit');
        Route::patch('/{user}', [UserController::class, 'update'])->name('admin.update');
        Route::post('/{user}/avatar', [UserController::class, 'updateAvatar'])->name('admin.updateAvatar');
        Route::delete('/{user}/avatar', [UserController::class, 'deleteAvatar'])->name('admin.deleteAvatar');
        Route::post('/{user}/restore', [UserController::class, 'restore'])->name('restore');
        Route::patch('/{user}/role', [UserController::class, 'updateRole'])->name('admin.updateRole');
        Route::patch('/{user}/password', [UserController::class, 'updatePassword'])->name('admin.updatePassword');
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::delete('/forceDelete/{user}', [UserController::class, 'forceDelete'])->name('forceDelete');
    });
});
