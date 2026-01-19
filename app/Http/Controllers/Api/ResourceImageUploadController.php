<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * API Upload d'image pour les ressources.
 *
 * @description
 * Permet d'uploader une image (fichier) et de récupérer une URL utilisable dans le champ `image`
 * (qui reste une string dans les endpoints bulk). Utile pour le quickedit / bulk.
 *
 * Sécurité :
 * - Auth obligatoire
 * - Seuls les admins (policy updateAny) peuvent uploader
 *
 * @example
 * POST /api/entities/resources/upload-image (multipart/form-data)
 * file: <image>
 */
class ResourceImageUploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $this->authorize('updateAny', Resource::class);

        $validated = $request->validate([
            'file' => ['required', 'file', 'image', 'max:5120'], // 5MB
        ]);

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $validated['file'];

        // Stockage public (accessible via Storage::url)
        $dir = 'uploads/entities/resources/images';
        $name = Str::random(24).'_'.time().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($dir, $name, ['disk' => 'public']);

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => "Impossible d'uploader l'image.",
            ], 500);
        }

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}

