<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class DataService
{
    const DISK_DEFAULT = 'public';

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
     * Vérifie si un chemin est bien dans le disque spécifié
     *
     * @param string $fullPath Le chemin complet
     * @param string $disk Le disque de stockage
     * @return bool True si le chemin est dans le disque
     */
    private static function isPathInDisk(string $fullPath, string $disk): bool
    {
        $diskPath = Storage::disk($disk)->path('');
        return str_starts_with($fullPath, $diskPath);
    }

    /**
     * Extrait et traite les données d'une requête, notamment les fichiers
     *
     * @param Request $request La requête HTTP
     * @param Object $obj L'objet modèle concerné
     * @param array $files Configuration des fichiers à traiter
     * @return array Les données traitées
     */
    public static function extractData(Request $request, Object $obj, array $files = []): array
    {
        $data = $request->validated();

        foreach ($files as $file) {
            if (!is_array($file)) {
                continue;
            }

            $fileConfig = self::formatArrayFile($file, $obj);
            if ($fileConfig === false) {
                continue;
            }

            // Créer les dossiers nécessaires avant d'extraire le fichier
            if (!self::createTreeDirectory($fileConfig['path_name'], $fileConfig['disk'])) {
                continue;
            }

            $path = self::extractFileAndStore($request, $obj, $fileConfig);
            if ($path !== false) {
                if ($fileConfig['is_multiple_files']) {
                    $data[$fileConfig['name_bd']] = $path;
                } else {
                    $data[$fileConfig['name_bd']] = $path;
                }
            }
        }

        return $data;
    }

    /**
     * Formate la configuration d'un fichier
     *
     * @param array $file Configuration brute du fichier
     * @param Object $obj L'objet modèle concerné
     * @return array|false Configuration formatée ou false si invalide
     */
    private static function formatArrayFile(array $file, Object $obj): array|false
    {
        if (!isset($file['name_bd']) || empty($file['name_bd'])) {
            return false;
        }

        return [
            'disk' => $file['disk'] ?? self::DISK_DEFAULT,
            'path_name' => $file['path_name'] ?? class_basename($obj),
            'file_name' => $file['file_name'] ?? '',
            'name_bd' => $file['name_bd'],
            'is_multiple_files' => $file['is_multiple_files'] ?? false,
            'compress' => $file['compress'] ?? false
        ];
    }

    /**
     * Extrait et stocke un fichier
     *
     * @param Request $request La requête HTTP
     * @param Object $obj L'objet modèle concerné
     * @param array $fileConfig Configuration du fichier
     * @return string|false Le chemin du fichier stocké ou false en cas d'erreur
     */
    private static function extractFileAndStore(Request $request, Object $obj, array $fileConfig): string|false
    {
        if (empty($fileConfig['name_bd']) || empty($fileConfig['path_name']) || empty($fileConfig['disk'])) {
            return false;
        }

        $inputFile = $request->file($fileConfig['name_bd']);
        if (!$inputFile || $inputFile->getError()) {
            return false;
        }

        // Supprimer l'ancien fichier s'il existe
        self::deleteFile($obj, $fileConfig['name_bd']);

        // Stocker le nouveau fichier
        $path = !empty($fileConfig['file_name'])
            ? $inputFile->storeAs($fileConfig['path_name'], $fileConfig['file_name'], $fileConfig['disk'])
            : $inputFile->store($fileConfig['path_name'], $fileConfig['disk']);

        if ($fileConfig['compress']) {
            $path = self::compressAndConvertImage($path, $fileConfig['disk']);
        }

        return $path;
    }

    /**
     * Supprime un fichier
     *
     * @param Object $obj L'objet modèle concerné
     * @param string $nameFile Nom du champ contenant le chemin du fichier
     * @param bool $isMultipleFile Si le fichier est multiple
     * @return bool Succès de la suppression
     */
    public static function deleteFile(Object $obj, string $nameFile = "", bool $isMultipleFile = false): bool
    {
        if (empty($nameFile)) {
            return false;
        }

        if ($isMultipleFile) {
            $files = $obj->getPathFiles();
            $success = true;
            foreach ($files as $file) {
                if (!Storage::disk('public')->delete($file)) {
                    $success = false;
                }
            }
            return $success;
        }

        if ($obj->$nameFile) {
            return Storage::disk('public')->delete($obj->$nameFile);
        }

        return false;
    }

    /**
     * Compresse et convertit une image
     *
     * @param string $path Chemin de l'image
     * @param string $disk Disque de stockage
     * @param int $quality Qualité de compression
     * @return string Chemin de l'image compressée
     */
    private static function compressAndConvertImage(string $path, string $disk = "public", int $quality = 70): string
    {
        $vectorFormats = ['svg', 'eps', 'pdf'];
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if (in_array(strtolower($extension), $vectorFormats)) {
            return $path;
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read(Storage::disk($disk)->path($path));

        // Convertir en WebP
        $newPath = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME) . '.webp';
        $image->toWebp($quality)->save(Storage::disk($disk)->path($newPath));

        // Supprimer l'ancien fichier
        Storage::disk($disk)->delete($path);

        return $newPath;
    }
}
