<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\JsonResponse;

/**
 * Contenu complet d’une section CMS (chargement différé après payload Inertia léger).
 */
class CmsSectionContentController extends Controller
{
    /**
     * Retourne data/settings pour une section visible par l’utilisateur courant.
     */
    public function show(Section $section): JsonResponse
    {
        $this->authorize('view', $section);

        $section->loadMissing('media');

        return response()->json([
            'id' => $section->id,
            'data' => $section->data ?? [],
            'settings' => $section->settings ?? [],
            'files' => $section->getMedia('files')->map(static function ($media) {
                return [
                    'id' => $media->id,
                    'file' => $media->getUrl(),
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : null,
                    'title' => $media->getCustomProperty('title'),
                    'comment' => $media->getCustomProperty('comment'),
                    'description' => $media->getCustomProperty('description'),
                ];
            })->values()->all(),
        ]);
    }
}
