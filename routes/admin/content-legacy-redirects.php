<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Anciennes URLs `/admin/{referentiel}` → `/admin/content/{referentiel}`.
 *
 * @example GET /admin/characteristics → 301 /admin/content/characteristics
 */
$legacyContentPrefixes = [
    'admin/characteristics' => 'admin/content/characteristics',
    'admin/effects' => 'admin/content/effects',
    'admin/sub-effects' => 'admin/content/sub-effects',
    'admin/languages' => 'admin/content/languages',
    'admin/scrapping-mappings' => 'admin/content/scrapping-mappings',
    'admin/dofusdb-effect-mappings' => 'admin/content/dofusdb-effect-mappings',
    'admin/dofus-conversion-formulas' => 'admin/content/dofus-conversion-formulas',
];

foreach ($legacyContentPrefixes as $from => $to) {
    Route::get($from.'/{path?}', function (Request $request, ?string $path = null) use ($to) {
        $suffix = ($path !== null && $path !== '') ? '/'.$path : '';
        $qs = $request->getQueryString();

        return redirect('/'.$to.$suffix.($qs ? '?'.$qs : ''), 301);
    })->where('path', '.*');
}
