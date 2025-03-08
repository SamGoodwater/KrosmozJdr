<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Inertia\Inertia;
use App\Rules\FileRules;
use Illuminate\Support\Facades\Storage;

$uniqidRegex = '[A-Za-z0-9]+';
$slugRegex = '[A-Za-z0-9]+(?:(-|_).[A-Za-z0-9]+)*';

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Users
Route::prefix('user')->name("user.")->middleware('auth')->group(function () use ($uniqidRegex) {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/edit', [UserController::class, 'edit'])->name('edit');
    Route::patch('/', [UserController::class, 'update'])->name('update');
    Route::post('/avatar', [UserController::class, 'updateAvatar'])->name('updateAvatar');
    Route::delete('/avatar', [UserController::class, 'deleteAvatar'])->name('deleteAvatar');
    Route::delete('/', [UserController::class, 'delete'])->name('delete');

    // Routes accessibles uniquement aux admins et super_admins
    Route::middleware('role:admin')->group(function () use ($uniqidRegex) {
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user:uniqid}/edit', [UserController::class, 'edit'])
        ->name('admin.edit')
        ->where('user', $uniqidRegex);
        Route::patch('/{user:uniqid}', [UserController::class, 'update'])
        ->name('admin.update')
        ->where('user', $uniqidRegex);
        Route::post('/{user:uniqid}/avatar', [UserController::class, 'updateAvatar'])
            ->name('admin.updateAvatar')
            ->where('user', $uniqidRegex);
        Route::delete('/{user:uniqid}/avatar', [UserController::class, 'deleteAvatar'])
            ->name('admin.deleteAvatar')
            ->where('user', $uniqidRegex);
        Route::post('/{user:uniqid}', [UserController::class, 'restore'])
        ->name('restore')
        ->where('user', $uniqidRegex);
    });

    // Routes accessibles uniquement aux super_admins
    Route::middleware('role:super_admin')->group(function () use ($uniqidRegex) {
        Route::delete('/forcedDelete/{user:uniqid}', [UserController::class, 'forcedDelete'])
        ->name('forcedDelete')
        ->where('user', $uniqidRegex);
    });
});
