<?php

use App\Http\Controllers\ChangelogMarkdownEditorController;
use App\Http\Controllers\ChangelogMarkdownFeedController;
use App\Http\Controllers\LegalMarkdownSourceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web — Légal & changelog Markdown (routes nommées, sans URL absolues dev/prod)
|--------------------------------------------------------------------------
*/

Route::get('/legal/cgu', [LegalMarkdownSourceController::class, 'cgu'])->name('legal.cgu');
Route::get('/legal/politique-donnees', [LegalMarkdownSourceController::class, 'politiqueDonnees'])->name('legal.politique-donnees');
Route::get('/legal/cookies', [LegalMarkdownSourceController::class, 'cookies'])->name('legal.cookies');

Route::get('/changelog/feed/{version}', [ChangelogMarkdownFeedController::class, 'show'])
    ->where('version', '[0-9]+\.[0-9]+\.[0-9]+')
    ->name('changelog.feed');

Route::get('/changelog/roadmap', [ChangelogMarkdownFeedController::class, 'roadmap'])
    ->name('changelog.roadmap');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/changelog/sources', [ChangelogMarkdownEditorController::class, 'index'])
        ->name('changelog.sources');
    Route::put('/changelog/sources/{name}', [ChangelogMarkdownEditorController::class, 'update'])
        ->where('name', 'intro|roadmap|[0-9]+\.[0-9]+\.[0-9]+')
        ->name('changelog.sources.update');
});
