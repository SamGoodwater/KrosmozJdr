<?php

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedbackThreadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web — Feedback utilisateur (retours bugs, suggestions, etc.)
|--------------------------------------------------------------------------
|
| Accessible sans authentification. Route throttlée pour limiter le spam.
|
*/

$throttle = config('feedback.throttle_per_minute', 6);

Route::post('/feedback', [FeedbackController::class, 'store'])
    ->name('feedback.store')
    ->middleware('throttle:'.$throttle.',1');

Route::middleware(['auth', 'verified'])->group(function () use ($throttle): void {
    Route::get('/feedback', [FeedbackThreadController::class, 'index'])
        ->name('feedback.index');
    Route::get('/feedback/{feedback}', [FeedbackThreadController::class, 'show'])
        ->name('feedback.show');
    Route::post('/feedback/{feedback}/messages', [FeedbackThreadController::class, 'storeMessage'])
        ->middleware('throttle:'.$throttle.',1')
        ->name('feedback.messages.store');
});
