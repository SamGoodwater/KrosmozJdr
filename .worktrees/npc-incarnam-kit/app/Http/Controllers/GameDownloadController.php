<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Rules\GameDownloadCatalog;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Téléchargement public des fichiers du catalogue (livre, fiches, logo).
 *
 * @example GET /telechargements/rules-pdf
 */
class GameDownloadController extends Controller
{
    public function show(string $key, GameDownloadCatalog $catalog): StreamedResponse
    {
        $item = $catalog->find($key);
        if ($item === null) {
            abort(404);
        }
        if (! $item['available']) {
            abort(404, 'Ce fichier n’est pas encore disponible.');
        }

        $configItem = collect(config('game_downloads.items', []))
            ->firstWhere('key', $key);
        $relative = is_array($configItem) ? $catalog->relativePath($configItem) : null;
        if ($relative === null) {
            abort(404);
        }

        $disk = Storage::disk((string) config('game_downloads.disk', 'public'));
        $downloadName = basename($relative);

        return $disk->download($relative, $downloadName, [
            'Content-Type' => (string) $item['mime'],
        ]);
    }
}
