<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

/**
 * Service de gestion des images
 *
 * @description
 * Service pour gérer les images dans l'application.
 * - Gestion des chemins d'images
 * - Support des icônes FontAwesome
 * - Gestion des thumbnails avec cache
 * - Conversion automatique en WebP
 * - Optimisation des images
 *
 * @example
 * // Générer un thumbnail
 * $thumbnailPath = $imageService->generateThumbnail('images/photo.jpg', [
 *     'width' => 300,
 *     'height' => 300,
 *     'fit' => 'cover',
 *     'quality' => 80
 * ]);
 *
 * // Convertir en WebP
 * $webpPath = $imageService->convertToWebp('images/photo.jpg');
 */
class ImageService
{
    private const DISK = 'public';

    private const CACHE_TTL = 3600; // 1 heure

    private const CACHE_PREFIX = 'image_';

    private const THUMBNAIL_PREFIX = 'thumbnails/';

    private const ORIGINAL_PREFIX = 'originals/';

    private const DEFAULT_QUALITY = 80;

    private const MAX_DIMENSION = 2000;

    private const SUPPORTED_FORMATS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    private const CACHE_TAGS = ['images', 'thumbnails'];

    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver);
    }

    /**
     * Normalise le chemin d'une image
     */
    public function normalizePath(string $path): string
    {
        // Nettoyer le chemin
        $path = trim($path, '/');

        // Si c'est une icône FontAwesome, retourner le chemin tel quel
        if ($this->isFontAwesome($path)) {
            return $path;
        }

        // Si le chemin commence déjà par originals/, le retourner tel quel
        if (str_starts_with($path, self::ORIGINAL_PREFIX)) {
            return $path;
        }

        // Pour les autres cas, ajouter originals/
        return self::ORIGINAL_PREFIX.$path;
    }

    /**
     * Vérifie si le chemin est une icône FontAwesome
     */
    public function isFontAwesome(string $path): bool
    {
        return str_starts_with($path, 'fa-') || str_starts_with($path, 'storage/images/fa-');
    }

    /**
     * Récupère le chemin complet d'une image
     */
    public function getFullPath(string $path): string
    {
        return Storage::disk(FileService::DISK_DEFAULT)->path($path);
    }

    /**
     * Vérifie si une image existe
     */
    public function exists(string $path): bool
    {
        return Storage::disk(FileService::DISK_DEFAULT)->exists($path);
    }

    /**
     * Génère un thumbnail pour une image avec cache optimisé
     */
    public function generateThumbnail(string $path, array $options = []): ?string
    {
        try {
            $disk = Storage::disk(FileService::DISK_DEFAULT);

            // Vérifier si l'image source existe
            if (! $disk->exists($path)) {
                \Log::warning('ImageService - Image source non trouvée:', ['path' => $path]);

                return null;
            }

            // Options par défaut
            $options = array_merge([
                'width' => 300,
                'height' => 300,
                'fit' => 'cover',
                'quality' => self::DEFAULT_QUALITY,
                'format' => 'webp',
            ], $options);

            $options['width'] = (int) $options['width'];
            $options['height'] = (int) $options['height'];
            $options['quality'] = (int) $options['quality'];

            // Limiter les dimensions
            $options['width'] = min($options['width'], self::MAX_DIMENSION);
            $options['height'] = min($options['height'], self::MAX_DIMENSION);

            // Générer le nom du fichier de cache
            $cachePath = $this->getCachePath($path, $options);
            $cacheKey = self::CACHE_PREFIX.md5($cachePath);

            // Vérifier le cache (tags = Redis/Memcached ; store « array » des tests → sans tags)
            return $this->rememberWithOptionalTags($cacheKey, function () use ($disk, $path, $options, $cachePath) {
                // Vérifier si le thumbnail existe déjà sur le disque
                if ($disk->exists($cachePath)) {
                    return $cachePath;
                }

                // Créer l'image avec Intervention/Imagick
                $image = $this->imageManager->read($disk->path($path));

                // Appliquer les transformations
                if ($options['fit'] === 'cover') {
                    $image->cover($options['width'], $options['height']);
                } else {
                    $image->contain($options['width'], $options['height']);
                }

                $targetPath = $disk->path($cachePath);
                $dir = dirname($cachePath);
                if ($dir !== '' && $dir !== '.') {
                    $disk->makeDirectory($dir);
                }
                $format = strtolower((string) ($options['format'] ?? 'webp'));
                $q = max(1, min(100, $options['quality']));

                match ($format) {
                    'jpg', 'jpeg' => $image->toJpeg($q)->save($targetPath),
                    'png' => $image->toPng()->save($targetPath),
                    'gif' => $image->toGif()->save($targetPath),
                    'webp' => $image->toWebp($q)->save($targetPath),
                    default => $image->toWebp($q)->save($targetPath),
                };

                return $cachePath;
            });
        } catch (\Exception $e) {
            \Log::error('ImageService - Erreur lors de la génération du thumbnail:', [
                'path' => $path,
                'options' => $options,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  Closure():mixed  $callback
     */
    protected function rememberWithOptionalTags(string $cacheKey, Closure $callback): mixed
    {
        if (Cache::supportsTags()) {
            return Cache::tags(self::CACHE_TAGS)->remember($cacheKey, self::CACHE_TTL, $callback);
        }

        return Cache::remember($cacheKey, self::CACHE_TTL, $callback);
    }

    /**
     * Génère le chemin de cache pour un thumbnail
     */
    protected function getCachePath(string $path, array $options): string
    {
        $info = pathinfo($path);
        $hash = md5(json_encode($options));
        $extension = $options['format'] ?? 'webp';

        return sprintf(
            'thumbnails/%s/%s_%s.%s',
            $info['dirname'],
            $info['filename'],
            $hash,
            $extension
        );
    }

    /**
     * Récupère l'URL publique d'une image
     */
    public function getPublicUrl(string $path): string
    {
        return Storage::disk(self::DISK)->url($this->normalizePath($path));
    }

    /**
     * Récupère l'URL publique d'un thumbnail
     */
    public function getThumbnailUrl(string $path, array $options = []): ?string
    {
        $thumbnailPath = $this->generateThumbnail($path, $options);

        return $thumbnailPath ? Storage::disk(self::DISK)->url($thumbnailPath) : null;
    }

    /**
     * Nettoie les thumbnails obsolètes
     *
     * @param  int  $olderThan  Age en secondes des thumbnails à supprimer (par défaut 24h)
     */
    public function cleanThumbnails(int $olderThan = 86400): void
    {
        try {
            $disk = Storage::disk(FileService::DISK_DEFAULT);
            $thumbnailsPath = 'thumbnails';

            if (! $disk->exists($thumbnailsPath)) {
                return;
            }

            $files = $disk->allFiles($thumbnailsPath);
            $now = time();

            foreach ($files as $file) {
                $lastModified = $disk->lastModified($file);
                if ($now - $lastModified > $olderThan) {
                    $disk->delete($file);
                }
            }

            \Log::info('ImageService - Nettoyage des thumbnails terminé', [
                'olderThan' => $olderThan,
                'filesDeleted' => count($files),
            ]);
        } catch (\Exception $e) {
            \Log::error('ImageService - Erreur lors du nettoyage des thumbnails:', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Convertit une image en format WebP
     *
     * @param  string  $path  Chemin de l'image source
     * @return string Nouveau chemin de l'image WebP
     *
     * @example
     * $webpPath = $imageService->convertToWebp('images/photo.jpg');
     */
    public function convertToWebp(string $path): string
    {
        $disk = Storage::disk(FileService::DISK_DEFAULT);
        $fullPath = $disk->path($path);

        // Créer une nouvelle image avec Intervention/Imagick
        $image = $this->imageManager->read($fullPath);

        // Générer le nouveau chemin
        $newPath = pathinfo($path, PATHINFO_DIRNAME).'/'.
            pathinfo($path, PATHINFO_FILENAME).'.webp';

        // Sauvegarder en WebP avec une qualité de 80%
        $image->toWebp(self::DEFAULT_QUALITY)->save($disk->path($newPath));

        // Supprimer l'ancien fichier
        $disk->delete($path);

        return $newPath;
    }
}
