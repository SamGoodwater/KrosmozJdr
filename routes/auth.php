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
use App\Http\Controllers\ProfileController;
use Inertia\Inertia;

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Users
Route::prefix('user')->name("user.")->middleware('auth')->group(function () use ($uniqidRegex) {
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/{user:uniqid}', [UserController::class, 'dashboard'])->name('admindashboard')->where('user', $uniqidRegex);
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::patch('/edit', [UserController::class, 'edit'])->name('edit');
    // Route::patch('/edit/{user:uniqid}', [UserController::class, 'edit'])->name('adminedit')->where('user', $uniqidRegex);
    Route::patch('/', [UserController::class, 'update'])->name('update');
    Route::patch('/{user:uniqid}', [UserController::class, 'update'])->name('admibupdate')->where('user', $uniqidRegex);
    Route::delete('/', [UserController::class, 'delete'])->name('delete');
    Route::delete('/{user:uniqid}', [UserController::class, 'delete'])->name('admindelete')->where('user', $uniqidRegex);
    Route::post('/{user:uniqid}', [UserController::class, 'restore'])->name('restore')->where('user', $uniqidRegex);
    Route::delete('/forcedDelete/{user:uniqid}', [UserController::class, 'forcedDelete'])->name('forcedDelete')->where('user', $uniqidRegex);
});
