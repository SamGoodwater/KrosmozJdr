<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;

class FileProcessionService
{
    const DISK_DEFAULT = 'public';

    const MAX_SIZE = 20480; // 20 Mo

    const EXTENSIONS_IMAGE = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'apng', 'bmp', 'ico', 'tiff'];
    const EXTENSIONS_VIDEO = ['mp4', 'webm', '3gp', 'fl', 'avi', 'mkv', 'mov', 'wmv', 'mpg', 'mpeg', 'vob', 'ogv', 'ogg', 'drc'];
    const EXTENSIONS_AUDIO = ['wav', 'mp3', 'ogg', 'flac', 'alac', 'aac', 'opus', 'webm', 'm4a', '3gp', 'amr'];
    const EXTENSIONS_DOCUMENT = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods', 'odp'];

    /**
     * Crée les dossiers nécessaires dans le chemin spécifié de manière sécurisée
     *
     * @param string $path Le chemin complet où créer les dossiers
     * @param string $disk Le disque de stockage à utiliser
     * @return bool True si les dossiers ont été créés avec succès
     */
    public static function createTreeDirectory(string $path, string $disk = self::DISK_DEFAULT): bool
    {
        try {
            // Vérifier que le chemin est valide et ne contient pas de caractères dangereux
            if (!self::isValidPath($path)) {
                throw new \Exception('Chemin invalide');
            }

            // Récupérer le chemin complet du disque
            $fullPath = Storage::disk($disk)->path($path);

            // Vérifier que le chemin est bien dans le disque spécifié
            if (!self::isPathInDisk($fullPath, $disk)) {
                throw new \Exception('Chemin hors du disque autorisé');
            }

            // Créer les dossiers s'ils n'existent pas
            if (!Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->makeDirectory($path);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création des dossiers:', [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Vérifie si un chemin est valide
     *
     * @param string $path Le chemin à vérifier
     * @return bool True si le chemin est valide
     */
    private static function isValidPath(string $path): bool
    {
        // Vérifier que le chemin ne contient pas de caractères dangereux
        if (preg_match('/[<>:"|?*]/', $path)) {
            return false;
        }

        // Vérifier que le chemin ne commence pas par des points ou des slashes multiples
        if (preg_match('/^\.|^\/+|\/\/+/', $path)) {
            return false;
        }

        // Vérifier que le chemin ne contient pas de séquences de points dangereuses
        if (preg_match('/\.\./', $path)) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si un chemin est dans un disque
     *
     * @param string $path Le chemin à vérifier
     * @param string $disk Le disque à vérifier
     * @return bool True si le chemin est dans le disque
     */
    private static function isPathInDisk(string $path, string $disk): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Vérifie si un chemin est une image
     *
     * @param string $path Le chemin à vérifier
     * @return bool True si le chemin est une image
     */
    public static function isImagePath(string $path): bool
    {
        return in_array(pathinfo($path, PATHINFO_EXTENSION), self::EXTENSIONS_IMAGE);
    }

    /**
     * Vérifie si un chemin est une vidéo
     *
     * @param string $path Le chemin à vérifier
     * @return bool True si le chemin est une vidéo
     */
    private static function isVideoPath(string $path): bool
    {
        return in_array(pathinfo($path, PATHINFO_EXTENSION), self::EXTENSIONS_VIDEO);
    }

    /**
     * Vérifie si un chemin est un audio
     *
     * @param string $path Le chemin à vérifier
     * @return bool True si le chemin est un audio
     */
    private static function isAudioPath(string $path): bool
    {
        return in_array(pathinfo($path, PATHINFO_EXTENSION), self::EXTENSIONS_AUDIO);
    }

    /**
     * Vérifie si un chemin est un document
     *
     * @param string $path Le chemin à vérifier
     * @return bool True si le chemin est un document
     */
    private static function isDocumentPath(string $path): bool
    {
        return in_array(pathinfo($path, PATHINFO_EXTENSION), self::EXTENSIONS_DOCUMENT);
    }

    /**
     * Vérifie si un chemin est un pdf
     *
     * @param string $path Le chemin à vérifier
     * @return bool True si le chemin est un pdf
     */
    private static function isPdfPath(string $path): bool
    {
        return pathinfo($path, PATHINFO_EXTENSION) === 'pdf';
    }

    /**
     * Vérifie si un chemin est un fichier
     *
     * @param string $path Le chemin à vérifier
     * @return bool True si le chemin est un fichier
     */
    private static function isFilePath(string $path): bool
    {
        return self::isImagePath($path) || self::isVideoPath($path) || self::isAudioPath($path) || self::isDocumentPath($path);
    }


    /**
     * Convertit une image en webp
     *
     * @param string $path Le chemin de l'image à convertir
     * @return string Le nouveau chemin de l'image webp
     */

    public function convertToWebp(string $path): string
    {
        // Conversion de l'image en webp, retourne le nouveau chemin
        return $path;
    }

    /**
     * Compresse une image
     *
     * @param string $path Le chemin de l'image à compresser
     * @param int $quality La qualité de la compression (0-100)
     * @return string Le nouveau chemin de l'image compressée
     */
    public function compressImage(string $path, int $quality = 80): string
    {
        // Compression de l'image, retourne le nouveau chemin
        return $path;
    }
}
