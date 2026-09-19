<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Rules\GameDownloadCatalog;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Téléchargement des fichiers du catalogue (livre joueur, atelier MJ, fiches, logo).
 *
 * @example GET /telechargements/rules-pdf
 */
class GameDownloadController extends Controller
{
    public function show(string $key, GameDownloadCatalog $catalog): StreamedResponse
    {
        $item = $catalog->configItem($key);
        if ($item === null) {
            abort(404);
        }
        $catalog->purgePublicCopyIfRestricted($item);
        if (! $catalog->userCanAccess($item)) {
            abort(403, 'Tu n’as pas accès à ce fichier.');
        }

        $listed = $catalog->find($key);
        if ($listed === null || ! $listed['available']) {
            abort(404, 'Ce fichier n’est pas encore disponible.');
        }

        $relative = $catalog->relativePath($item);
        if ($relative === null) {
            abort(404);
        }

        $disk = $catalog->diskFor($item);
        $downloadName = basename($relative);

        return $disk->download($relative, $downloadName, [
            'Content-Type' => (string) ($listed['mime'] ?? 'application/octet-stream'),
        ]);
    }
}
