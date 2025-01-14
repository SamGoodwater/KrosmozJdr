<?php

// use App\Http\Controllers\Auth\AuthenticatedSessionController;
// use App\Http\Controllers\Auth\ConfirmablePasswordController;
// use App\Http\Controllers\Auth\EmailVerificationNotificationController;
// use App\Http\Controllers\Auth\EmailVerificationPromptController;
// use App\Http\Controllers\Auth\NewPasswordController;
// use App\Http\Controllers\Auth\PasswordController;
// use App\Http\Controllers\Auth\PasswordResetLinkController;
// use App\Http\Controllers\Auth\RegisteredUserController;
// use App\Http\Controllers\Auth\VerifyEmailController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use Illuminate\Support\Facades\Route;

// Route::middleware('guest')->group(function () {
//     Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
//         ->name('password.request');

//     Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
//         ->name('password.email');

//     Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
//         ->name('password.reset');

//     Route::post('reset-password', [NewPasswordController::class, 'store'])
//         ->name('password.store');
// });

// Auth
Route::prefix('connexion')->name("login.")->controller(LoginController::class)->middleware('guest')->group(function () {
    Route::get('/', 'show')->name('show');
    Route::post('/login', 'login')->name('login');
});

Route::prefix('inscription')->name("register.")->controller(RegisterController::class)->group(function () {
    Route::get('/', 'show')->name('show');
    Route::post('/add', 'add')->name('add');
});

Route::prefix('auth')->name("auth.")->controller(AuthController::class)->group(function () use ($uniqidRegex) {
    Route::delete('/logout', 'logout')->name('logout');
    Route::get('/confirm_password[user:uniqid]', 'confirm_password_show')->name('confirm_password_show')->where('user', $uniqidRegex);
    Route::post('/confirm_password_request', 'confirm_password_request')->name('confirm_password_request');
    Route::get('/forget_password[user:uniqid]', 'forget_password_show')->name('forget_password_show')->where('user', $uniqidRegex);
    Route::post('/forget_password_request', 'forget_password_request')->name('forget_password_request');
    Route::get('/reset_password[user:uniqid]', 'reset_password_show')->name('reset_password_show')->where('user', $uniqidRegex);
    Route::post('/reset_password_request', 'reset_password_request')->name('reset_password_request');
    Route::get('/verify_email[user:uniqid]', 'verify_email_show')->name('verify_email_show')->where('user', $uniqidRegex);
    Route::post('/verify_email_request', 'verify_email_request')->name('verify_email_request');
});

// API
Route::get('/checkuserlogged', function () {
    return response()->json(['isLoggedIn' => Auth::check()]);
})->middleware('auth');

// Users
Route::prefix('user')->name("user.")->controller(UserController::class)->middleware(['auth', 'verified'])->group(function () use ($uniqidRegex) {
    Route::inertia('/', 'index')->name('index');
    Route::inertia('/{user:uniqid}', 'show')->name('show')->where('user', $uniqidRegex);
    Route::inertia('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::inertia('/{user:uniqid}/edit', 'edit')->name('edit')->where('user', $uniqidRegex);
    Route::patch('/{user:uniqid}', 'update')->name('update')->where('user', $uniqidRegex);
    Route::delete('/{user:uniqid}', 'delete')->name('delete')->where('user', $uniqidRegex);
    Route::post('/{user:uniqid}', 'restore')->name('restore')->where('user', $uniqidRegex);
    Route::delete('/forcedDelete/{user:uniqid}', 'forcedDelete')->name('forcedDelete')->where('user', $uniqidRegex);
});

// Route::middleware('auth')->group(function () {
//     Route::get('verify-email', EmailVerificationPromptController::class)
//         ->name('verification.notice');

//     Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
//         ->middleware(['signed', 'throttle:6,1'])
//         ->name('verification.verify');

//     Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//         ->middleware('throttle:6,1')
//         ->name('verification.send');

//     Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
//         ->name('password.confirm');

//     Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

//     Route::put('password', [PasswordController::class, 'update'])->name('password.update');

//     Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
//         ->name('logout');
// });
