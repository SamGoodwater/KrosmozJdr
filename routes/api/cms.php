<?php

use App\Http\Controllers\Api\CmsKrefEntityPreviewController;
use App\Http\Controllers\Api\CmsPageSectionPickerController;
use App\Http\Controllers\Api\CmsSectionPreviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — CMS (mentions pages / sections)
|--------------------------------------------------------------------------
*/

Route::middleware(['web'])->prefix('cms')->group(function () {
    Route::get('/page-section-picker', [CmsPageSectionPickerController::class, 'index'])
        ->name('api.cms.page-section-picker');
    Route::get('/section-preview-snippet', [CmsSectionPreviewController::class, 'showByQuery'])
        ->name('api.cms.sections.preview-snippet-query');
    Route::get('/sections/{section}/preview-snippet', [CmsSectionPreviewController::class, 'show'])
        ->name('api.cms.sections.preview-snippet');
    Route::get('/kref-entity-preview', [CmsKrefEntityPreviewController::class, 'show'])
        ->name('api.cms.kref-entity-preview');
});
