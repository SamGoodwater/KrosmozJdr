<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Api\Auth\AuthController;

$uniqidRegex = '[A-Za-z0-9]+';
$slugRegex = '[A-Za-z0-9]+(?:(-|_).[A-Za-z0-9]+)*';


// Auth
Route::prefix('auth')->name("auth.")->controller(AuthController::class)->group(function () {
    Route::get('is_logged', 'isLogged')->name('isLogged');
    Route::get('/', 'userLogged')->name('userLogged');
});

Route::get('/', function () {
    return response()->json([
        'status' => 'success'
    ]);
});
