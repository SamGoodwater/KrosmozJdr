<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPrivacyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web — Utilisateur (profil, gestion admin)
|--------------------------------------------------------------------------
*/

Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    Route::post('/password/confirm', [UserController::class, 'confirmPassword'])
        ->middleware('throttle:privacy-actions')
        ->name('password.confirm');

    Route::get('/', [UserController::class, 'show'])->name('show');
    Route::get('/edit', [UserController::class, 'edit'])->name('edit');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::patch('/', [UserController::class, 'update'])->name('update');
    Route::patch('/password', [UserController::class, 'updatePassword'])->name('updatePassword');
    Route::post('/avatar', [UserController::class, 'updateAvatar'])->name('updateAvatar');
    Route::delete('/avatar', [UserController::class, 'deleteAvatar'])->name('deleteAvatar');
    Route::delete('/', [UserController::class, 'delete'])->name('delete');

    Route::prefix('/privacy')->name('privacy.')->group(function () {
        Route::get('/', [UserPrivacyController::class, 'index'])->name('index');
        Route::post('/export', [UserPrivacyController::class, 'requestExport'])
            ->middleware(['password.confirm', 'throttle:privacy-actions'])
            ->name('export');
        Route::get('/exports/{privacyExport}', [UserPrivacyController::class, 'downloadExport'])
            ->middleware(['signed', 'throttle:privacy-actions'])
            ->name('exports.download');
        Route::post('/delete/request', [UserPrivacyController::class, 'requestDeletion'])
            ->middleware(['password.confirm', 'throttle:privacy-actions'])
            ->name('delete.request');
        Route::post('/delete/cancel', [UserPrivacyController::class, 'cancelDeletionRequest'])
            ->middleware(['throttle:privacy-actions'])
            ->name('delete.cancel');
    });

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
