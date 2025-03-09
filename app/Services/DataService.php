<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
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

            // Utiliser handleFileUpload pour gérer l'upload
            if ($request->hasFile($fileConfig['name_bd'])) {
                $uploadResult = self::handleFileUpload($request->file($fileConfig['name_bd']), [
                    'disk' => $fileConfig['disk'],
                    'path' => $fileConfig['path_name'],
                    'fileName' => $fileConfig['file_name'],
                    'replace' => $obj->{$fileConfig['name_bd']} ?? null,
                    'shouldOptimizeImage' => $fileConfig['compress'],
                    'convertToWebp' => $fileConfig['compress'],
                    'quality' => 70
                ]);

                if ($uploadResult['success']) {
                    $data[$fileConfig['name_bd']] = $uploadResult['path'];
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
     * Gère l'upload d'un fichier avec validation et optimisation
     *
     *  $result = DataService::handleFileUpload($request->file('file'), [
     *       'path' => 'documents',
     *       'allowedTypes' => ['.pdf', '.doc', '.docx'],
     *       'shouldOptimizeImage' => false
     *  ]);
     *
     * @param \Illuminate\Http\UploadedFile $file Le fichier uploadé
     * @param array $options Les options de configuration
     * @return array{success: bool, path: string|null, message: string} Résultat de l'opération
     */
    public static function handleFileUpload($file, array $options = []): array
    {
        try {
            // Options par défaut
            $defaultOptions = [
                'disk' => self::DISK_DEFAULT,
                'path' => 'uploads',
                'maxSize' => 5242880, // 5MB
                'allowedTypes' => null,
                'shouldOptimizeImage' => true,
                'maxWidth' => 1920,
                'maxHeight' => 1080,
                'quality' => 70,
                'convertToWebp' => true,
                'fileName' => null,
                'replace' => null,
                'deleteOldFile' => null,
            ];

            $options = array_merge($defaultOptions, $options);

            // Validation de base
            if (!$file || !$file->isValid()) {
                throw new \Exception('Fichier invalide ou corrompu');
            }

            // Gestion du remplacement de fichier
            $oldFilePath = null;
            if ($options['replace'] && Storage::disk($options['disk'])->exists($options['replace'])) {
                $oldFilePath = $options['replace'];
                // Supprimer l'ancien fichier avant l'upload
                Storage::disk($options['disk'])->delete($oldFilePath);
            }

            if ($options['deleteOldFile'] && Storage::disk($options['disk'])->exists($options['deleteOldFile'])) {
                \Log::info('Suppression de l\'ancien fichier', [
                    'path' => $options['deleteOldFile'],
                    'exists' => Storage::disk($options['disk'])->exists($options['deleteOldFile'])
                ]);
                Storage::disk($options['disk'])->delete($options['deleteOldFile']);
            }

            // Nettoyer les anciens fichiers dans le dossier
            self::cleanOldFiles($options['path'], $options['disk']);

            try {
                // Création du nom de fichier
                $fileName = $options['fileName'] ?? (uniqid() . '_' . time());
                $extension = $file->getClientOriginalExtension();

                // Traitement spécial pour les images
                if ($options['shouldOptimizeImage'] && str_starts_with($file->getMimeType(), 'image/')) {
                    // Ne pas traiter les formats vectoriels
                    if (!in_array($extension, ['svg', 'eps', 'pdf'])) {
                        $manager = new ImageManager(new Driver());
                        $image = $manager->read($file->getRealPath());

                        // Redimensionnement si nécessaire
                        if ($image->width() > $options['maxWidth'] || $image->height() > $options['maxHeight']) {
                            $image->scaleDown(width: $options['maxWidth'], height: $options['maxHeight']);
                        }

                        // Conversion en WebP si demandé
                        if ($options['convertToWebp']) {
                            $fileName .= '.webp';
                            $fullPath = $options['path'] . '/' . $fileName;
                            Storage::disk($options['disk'])->put(
                                $fullPath,
                                (string) $image->toWebp($options['quality'])
                            );
                        } else {
                            $fileName .= '.' . $extension;
                            $fullPath = $options['path'] . '/' . $fileName;
                            Storage::disk($options['disk'])->put(
                                $fullPath,
                                (string) $image->encode(new AutoEncoder($options['quality']))
                            );
                        }
                    } else {
                        // Pour les formats vectoriels, stocker tel quel
                        $fileName .= '.' . $extension;
                        $fullPath = $file->storeAs($options['path'], $fileName, $options['disk']);
                    }
                } else {
                    // Pour les fichiers non-image, stocker tel quel
                    $fileName .= '.' . $extension;
                    $fullPath = $file->storeAs($options['path'], $fileName, $options['disk']);
                }

                // Vérifier que le nouveau fichier a bien été créé
                if (!Storage::disk($options['disk'])->exists($fullPath)) {
                    throw new \Exception('Le nouveau fichier n\'a pas été correctement créé');
                }

                return [
                    'success' => true,
                    'path' => $fullPath,
                    'message' => 'Fichier traité avec succès'
                ];
            } catch (\Exception $e) {
                // En cas d'erreur, supprimer le fichier partiellement uploadé
                if (isset($fullPath) && Storage::disk($options['disk'])->exists($fullPath)) {
                    Storage::disk($options['disk'])->delete($fullPath);
                }
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors du traitement du fichier:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'path' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Nettoie les anciens fichiers dans un dossier
     *
     * @param string $path Chemin à nettoyer
     * @param string $disk Disque de stockage
     */
    private static function cleanOldFiles(string $path, string $disk = self::DISK_DEFAULT): void
    {
        try {
            $files = Storage::disk($disk)->files($path);
            foreach ($files as $file) {
                // Supprimer les fichiers de plus de 1 heure
                if (time() - Storage::disk($disk)->lastModified($file) > 3600) {
                    Storage::disk($disk)->delete($file);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors du nettoyage des fichiers:', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Supprime un fichier
     *
     * @param string|object $target Le chemin du fichier ou l'objet contenant le chemin
     * @param string|null $propertyName Le nom de la propriété contenant le chemin (si $target est un objet)
     * @param string $disk Le disque de stockage à utiliser
     * @return bool True si la suppression a réussi
     */
    public static function deleteFile(string|object $target, ?string $propertyName = null, string $disk = self::DISK_DEFAULT): bool
    {
        try {
            // Si c'est un objet, on récupère le chemin depuis la propriété
            if (is_object($target) && $propertyName) {
                if (!property_exists($target, $propertyName)) {
                    throw new \Exception("La propriété $propertyName n'existe pas dans l'objet");
                }
                $filePath = $target->$propertyName;
            } else {
                // Si c'est une chaîne, on l'utilise directement comme chemin
                $filePath = $target;
            }

            // Vérifier que le chemin est valide
            if (empty($filePath)) {
                return false;
            }

            // Supprimer le fichier s'il existe
            if (Storage::disk($disk)->exists($filePath)) {
                return Storage::disk($disk)->delete($filePath);
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du fichier:', [
                'target' => $target,
                'propertyName' => $propertyName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
