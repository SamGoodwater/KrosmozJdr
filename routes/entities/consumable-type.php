<?php

use App\Http\Controllers\Type\ConsumableTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('entities/consumable-types')->name('entities.consumable-types.')->middleware(['auth', 'role:game_master', 'content.area', 'password.confirm'])->group(function () {
    Route::get('/', [ConsumableTypeController::class, 'index'])->name('index');
});
