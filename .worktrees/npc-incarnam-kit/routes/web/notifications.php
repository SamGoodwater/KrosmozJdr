<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web — Notifications
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/scrapping/start', [NotificationController::class, 'startScrappingNotification'])->name('scrapping.start');
    Route::patch('/scrapping/{id}', [NotificationController::class, 'updateScrappingNotification'])->name('scrapping.update');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::post('/{id}/archive', [NotificationController::class, 'archive'])->name('archive');
    Route::post('/{id}/unarchive', [NotificationController::class, 'unarchive'])->name('unarchive');
    Route::post('/{id}/pin', [NotificationController::class, 'pin'])->name('pin');
    Route::post('/{id}/unpin', [NotificationController::class, 'unpin'])->name('unpin');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
});
