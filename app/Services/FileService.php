<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileService
{
    // Constantes pour les extensions autorisées
    public const EXTENSIONS_IMAGE = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    public const EXTENSIONS_VIDEO = ['mp4', 'webm', 'ogg'];

    public const EXTENSIONS_AUDIO = ['mp3', 'wav', 'ogg'];

    public const EXTENSIONS_DOCUMENT = [
        'pdf',
        'txt',
        'csv',
        'rtf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'odt',
        'ods',
        'odp',
    ];

    public const EXTENSIONS_ARCHIVE = ['zip', 'rar', '7z', 'tar', 'gz'];

    // Taille maximale en Ko
    public const MAX_SIZE = 10240; // 10 Mo

    // Disque de stockage par défaut
    public const DISK_DEFAULT = 'public';

    /**
     * Vérifie si un chemin correspond à une image
     */
    public static function isImagePath(string $path): bool
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::EXTENSIONS_IMAGE);
    }

    /**
     * Retourne toutes les extensions autorisées
     */
    public static function getAllowedExtensions(): array
    {
        return array_merge(
            self::EXTENSIONS_IMAGE,
            self::EXTENSIONS_VIDEO,
            self::EXTENSIONS_AUDIO,
            self::EXTENSIONS_DOCUMENT,
            self::EXTENSIONS_ARCHIVE,
        );
    }

    /**
     * Extensions acceptées en upload utilisateur (hors SVG, contenu XML exécutable).
     *
     * `EXTENSIONS_IMAGE` garde `svg` pour reconnaître les icônes statiques déjà versionnées.
     *
     * @return list<string>
     */
    public static function getAllowedUploadExtensions(): array
    {
        return array_values(array_filter(
            self::getAllowedExtensions(),
            static fn (string $ext): bool => $ext !== 'svg',
        ));
    }

    /**
     * True si le fichier envoyé est un SVG (extension ou MIME), donc exécutable.
     *
     * @example FileService::isSvgUpload($request->file('file'))
     */
    public static function isSvgUpload(UploadedFile $file): bool
    {
        $mime = strtolower((string) $file->getMimeType());
        $ext = strtolower((string) $file->getClientOriginalExtension());

        return $ext === 'svg' || str_contains($mime, 'svg');
    }
}
