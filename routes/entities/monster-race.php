<?php

use App\Http\Controllers\Type\MonsterRaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('entities/monster-races')->name('entities.monster-races.')->middleware(['auth', 'role:game_master', 'content.area', 'password.confirm'])->group(function () {
    Route::get('/', [MonsterRaceController::class, 'index'])->name('index');
});
