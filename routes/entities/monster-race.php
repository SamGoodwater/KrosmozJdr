<?php

use App\Http\Controllers\Type\MonsterRaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('entities/monster-races')->name('entities.monster-races.')->middleware('auth')->group(function () {
    Route::get('/', [MonsterRaceController::class, 'index'])->name('index');
});

