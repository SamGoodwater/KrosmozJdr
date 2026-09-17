<?php

use Illuminate\Support\Facades\Route;

/**
 * Ancienne URL `/scrapping` : redirigée vers l’atelier contenu.
 */
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/scrapping', function () {
        return redirect()->route('admin.content.dofusdb.index');
    })->name('scrapping.index');
});
