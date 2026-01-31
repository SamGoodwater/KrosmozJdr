<?php

use App\Http\Controllers\Type\ItemTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('entities/item-types')->name('entities.item-types.')->middleware('auth')->group(function () {
    Route::get('/', [ItemTypeController::class, 'index'])->name('index');
});

