<?php

namespace App\Services;

class FileService
{
    // Constantes pour les extensions autorisées
    public const EXTENSIONS_IMAGE = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    public const EXTENSIONS_VIDEO = ['mp4', 'webm', 'ogg'];
    public const EXTENSIONS_AUDIO = ['mp3', 'wav', 'ogg'];
    public const EXTENSIONS_DOCUMENT = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

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
            self::EXTENSIONS_DOCUMENT
        );
    }
}
