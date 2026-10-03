<?php

use App\Http\Controllers\Type\SpellTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('entities/spell-types')->name('entities.spell-types.')->middleware(['auth', 'role:game_master', 'content.area', 'password.confirm'])->group(function () {
    Route::get('/', [SpellTypeController::class, 'index'])->name('index');
});
