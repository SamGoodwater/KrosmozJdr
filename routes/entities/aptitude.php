<?php

use App\Http\Controllers\Entity\AptitudeController;
use Illuminate\Support\Facades\Route;

Route::prefix('entities/aptitudes')->name('entities.aptitudes.')->group(function () {
    Route::get('/', [AptitudeController::class, 'index'])->name('index');
    Route::get('/{aptitude}', [AptitudeController::class, 'show'])->whereNumber('aptitude')->name('show');
});

Route::prefix('entities/aptitudes')->name('entities.aptitudes.')->middleware('auth')->group(function () {
    Route::get('/create', [AptitudeController::class, 'create'])->name('create');
    Route::post('/', [AptitudeController::class, 'store'])->name('store');
    Route::get('/{aptitude}/edit', [AptitudeController::class, 'edit'])->name('edit');
    Route::get('/{aptitude}/pdf', [AptitudeController::class, 'downloadPdf'])->name('pdf');
    Route::patch('/{aptitude}', [AptitudeController::class, 'update'])->name('update');
    Route::delete('/{aptitude}', [AptitudeController::class, 'delete'])->name('delete');
});
