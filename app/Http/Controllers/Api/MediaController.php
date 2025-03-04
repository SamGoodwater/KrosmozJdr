<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\FileRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MediaController extends Controller
{
    private const CACHE_KEY = 'media_files';
    private const CACHE_DURATION = 3600; // 1 heure

    /**
     * Récupère la liste des fichiers médias d'un type spécifique
     */
    public function index(string $type = 'image'): JsonResponse
    {
        $files = $this->getCachedFiles($type);
        return response()->json($files);
    }

    /**
     * Récupère un fichier média spécifique
     */
    public function show(string $type, string $directory, string $name): JsonResponse
    {
        $files = $this->getCachedFiles($type);

        if (isset($files[$directory][$name])) {
            return response()->json([
                'path' => $files[$directory][$name]
            ]);
        }

        return response()->json(['error' => 'Fichier non trouvé'], 404);
    }

    /**
     * Rafraîchit le cache des fichiers médias
     */
    public function refreshCache(): JsonResponse
    {
        Cache::forget(self::CACHE_KEY);
        $this->getCachedFiles();
        return response()->json(['message' => 'Cache rafraîchi avec succès']);
    }

    /**
     * Récupère les fichiers du cache ou les met à jour si nécessaire
     */
    private function getCachedFiles(string $type = null): array
    {
        return Cache::remember(self::CACHE_KEY . ($type ? "_{$type}" : ''), self::CACHE_DURATION, function () use ($type) {
            return $this->scanMediaFiles($type);
        });
    }

    /**
     * Scanne les fichiers médias dans le stockage
     */
    private function scanMediaFiles(?string $type): array
    {
        $files = [];
        $extensions = $this->getExtensionsForType($type);
        $basePath = Storage::disk('public')->path('');

        if (!is_dir($basePath)) {
            return $files;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                if (empty($extensions) || in_array($extension, $extensions)) {
                    // Obtenir le chemin relatif par rapport au dossier public
                    $relativePath = str_replace($basePath, '', $file->getPathname());
                    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

                    // Extraire le nom du fichier et le répertoire
                    $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $directory = dirname($relativePath);

                    // Si le fichier est dans le dossier racine images
                    if ($directory === 'images') {
                        if (!isset($files['images'])) {
                            $files['images'] = [];
                        }
                        $files['images'][$fileName] = Storage::disk('public')->url($relativePath);
                        continue;
                    }

                    // Pour les autres fichiers, garder la structure complète du chemin
                    if (!isset($files[$directory])) {
                        $files[$directory] = [];
                    }
                    $files[$directory][$fileName] = Storage::disk('public')->url($relativePath);
                }
            }
        }

        return $files;
    }

    /**
     * Récupère les extensions autorisées pour un type de fichier
     */
    private function getExtensionsForType(?string $type): array
    {
        if (!$type) {
            return [];
        }

        $extensionsString = match ($type) {
            FileRules::TYPE_IMAGE => FileRules::IMAGE,
            FileRules::TYPE_VIDEO => FileRules::VIDEO,
            FileRules::TYPE_AUDIO => FileRules::AUDIO,
            FileRules::TYPE_DOCUMENT => FileRules::DOCUMENT,
            default => '',
        };

        return explode(',', $extensionsString);
    }
}
