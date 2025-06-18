<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Services\FileService;

class ImageController extends Controller
{
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Affiche une image
     */
    public function show(string $path): Response
    {
        try {
            // Vérifier si c'est une icône FontAwesome
            if ($this->imageService->isFontAwesome($path)) {
                return response()->json([
                    'error' => 'Les icônes FontAwesome ne sont pas supportées pour cette route'
                ], 400);
            }

            // Vérifier si l'image existe
            if (!$this->imageService->exists($path)) {
                return response()->json([
                    'error' => 'Image non trouvée',
                    'path' => $path
                ], 404);
            }

            // Vérifier le type MIME
            $mimeType = Storage::disk(FileService::DISK_DEFAULT)->mimeType($path);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            
            if (!in_array($mimeType, $allowedMimes)) {
                return response()->json([
                    'error' => 'Type de fichier non autorisé',
                    'mime' => $mimeType
                ], 400);
            }

            // Retourner l'image avec les headers de sécurité
            return response()->file(
                $this->imageService->getFullPath($path),
                [
                    'Content-Type' => $mimeType,
                    'Cache-Control' => 'public, max-age=31536000',
                    'X-Content-Type-Options' => 'nosniff',
                    'X-Frame-Options' => 'DENY'
                ]
            );
        } catch (\Exception $e) {
            Log::error('ImageController - Erreur lors de l\'affichage de l\'image:', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Erreur lors de l\'affichage de l\'image',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche un thumbnail
     */
    public function thumbnail(Request $request, string $path): Response
    {
        try {
            // Vérifier si c'est une icône FontAwesome
            if ($this->imageService->isFontAwesome($path)) {
                return response()->json([
                    'error' => 'Les icônes FontAwesome ne sont pas supportées pour cette route'
                ], 400);
            }

            // Valider les paramètres
            $validated = $request->validate([
                'w' => 'nullable|integer|min:1|max:2000',
                'h' => 'nullable|integer|min:1|max:2000',
                'fit' => 'nullable|in:contain,cover',
                'q' => 'nullable|integer|min:1|max:100',
                'fm' => 'nullable|in:jpg,jpeg,png,gif,webp',
            ]);

            // Convertir les paramètres pour le service
            $options = [
                'width' => $validated['w'] ?? 300,
                'height' => $validated['h'] ?? 300,
                'fit' => $validated['fit'] ?? 'cover',
                'quality' => $validated['q'] ?? 80,
                'format' => $validated['fm'] ?? 'webp'
            ];

            // Générer le thumbnail
            $thumbnailPath = $this->imageService->generateThumbnail($path, $options);

            if (!$thumbnailPath) {
                return response()->json([
                    'error' => 'Thumbnail non généré',
                    'path' => $path
                ], 404);
            }

            // Retourner le thumbnail
            return response()->file(
                $this->imageService->getFullPath($thumbnailPath),
                [
                    'Content-Type' => 'image/' . $options['format'],
                    'Cache-Control' => 'public, max-age=31536000'
                ]
            );
        } catch (\Exception $e) {
            Log::error('ImageController - Erreur lors de la génération du thumbnail:', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la génération du thumbnail',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Nettoie les thumbnails
     */
    public function cleanThumbnails(): Response
    {
        try {
            $this->imageService->cleanThumbnails();
            return response()->json(['message' => 'Thumbnails nettoyés avec succès']);
        } catch (\Exception $e) {
            Log::error('ImageController - Erreur lors du nettoyage des thumbnails:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Erreur lors du nettoyage des thumbnails',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
