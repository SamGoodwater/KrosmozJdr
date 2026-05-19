<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * @description Sert les documents légaux en Markdown depuis `storage/app/public/legal/` pour les sections CMS (`legal_markdown`, fetch same-origin).
 *
 * @example La route nommée `legal.cgu` renvoie le corps markdown de `legal/cgu.md`.
 */
final class LegalMarkdownSourceController extends Controller
{
    /**
     * @var array<string, string> route name suffix sans préfixe `legal.` → nom de fichier
     */
    private const DOCUMENTS = [
        'cgu' => 'cgu.md',
        'politique-donnees' => 'politique-donnees.md',
        'cookies' => 'cookies.md',
    ];

    public function cgu(): Response
    {
        return $this->respond('cgu.md');
    }

    public function politiqueDonnees(): Response
    {
        return $this->respond('politique-donnees.md');
    }

    public function cookies(): Response
    {
        return $this->respond('cookies.md');
    }

    private function respond(string $filename): Response
    {
        $path = storage_path('app/public/legal/'.$filename);
        if (! is_readable($path)) {
            abort(503, 'Document légal indisponible.');
        }

        /** @var string|false $contents */
        $contents = file_get_contents($path);
        if ($contents === false) {
            abort(503, 'Document légal indisponible.');
        }

        return response($contents, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Cache-Control' => 'public, max-age=120',
        ]);
    }
}
