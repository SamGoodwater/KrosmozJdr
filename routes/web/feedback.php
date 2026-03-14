<?php

use App\Http\Controllers\FeedbackController;
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
    ->middleware('throttle:' . $throttle . ',1');
