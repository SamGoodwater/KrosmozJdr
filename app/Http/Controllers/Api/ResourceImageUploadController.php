<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity\Resource;
use App\Models\EntityImageUpload;
use App\Services\Media\EntityImageMediaService;
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
    public function __construct(
        private EntityImageMediaService $entityImageMediaService,
    ) {}

    public function upload(Request $request): JsonResponse
    {
        $this->authorize('updateAny', Resource::class);

        $validated = $request->validate([
            'resource_id' => ['sometimes', 'integer', 'exists:resources,id'],
        ]);

        $resourceId = $validated['resource_id'] ?? null;

        if ($resourceId) {
            $resource = Resource::findOrFail($resourceId);
            $this->authorize('update', $resource);
            $resource->clearMediaCollection('images');
            $media = $this->entityImageMediaService->attachFromRequest($resource, $request, 'file', 'images', 'image');
        } else {
            $placeholder = EntityImageUpload::create();
            $media = $this->entityImageMediaService->attachFromRequest($placeholder, $request, 'file', 'images');
        }

        return response()->json(array_merge(
            ['success' => true],
            $this->entityImageMediaService->mediaPayload($media),
        ));
    }
}
