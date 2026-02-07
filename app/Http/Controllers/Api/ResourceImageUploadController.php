<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity\Resource;
use App\Models\EntityImageUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Upload d'image pour les ressources (Spatie Media Library).
 *
 * Si resource_id est fourni : attache l'image à la ressource (collection images) et met à jour image.
 * Sinon (bulk) : attache à un placeholder EntityImageUpload et retourne l'URL à affecter aux entités.
 *
 * Réponse attendue par le front : { success: true, url: "..." }
 */
class ResourceImageUploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $this->authorize('updateAny', Resource::class);

        $validated = $request->validate([
            'file' => ['required', 'file', 'image', 'max:5120'], // 5MB
            'resource_id' => ['sometimes', 'integer', 'exists:resources,id'],
        ]);

        $resourceId = $validated['resource_id'] ?? null;

        if ($resourceId) {
            $resource = Resource::findOrFail($resourceId);
            $this->authorize('update', $resource);
            $resource->clearMediaCollection('images');
            $media = $resource->addMediaFromRequest('file')->toMediaCollection('images');
            $resource->update(['image' => $media->getUrl()]);
            $url = $media->getUrl();
        } else {
            $placeholder = EntityImageUpload::create();
            $media = $placeholder->addMediaFromRequest('file')->toMediaCollection('images');
            $url = $media->getUrl();
        }

        return response()->json([
            'success' => true,
            'path' => $media->getPath(),
            'url' => $url,
        ]);
    }
}

