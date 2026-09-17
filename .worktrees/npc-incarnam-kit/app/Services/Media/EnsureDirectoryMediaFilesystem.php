<?php

declare(strict_types=1);

namespace App\Services\Media;

use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\RemoteFile;

/**
 * Filesystem Media Library qui s'assure que le répertoire de destination existe
 * avant chaque écriture.
 *
 * Évite les échecs silencieux ou incohérents lorsque les dossiers MEDIA_PATH
 * (ex: images/entity/spells/) n'existent pas encore, notamment lors du scrapping.
 */
class EnsureDirectoryMediaFilesystem extends Filesystem
{
    public function copyToMediaLibrary(string $pathToFile, Media $media, ?string $type = null, ?string $targetFileName = null): void
    {
        $this->ensureMediaDirectoryExists($media, $type);

        parent::copyToMediaLibrary($pathToFile, $media, $type, $targetFileName);
    }

    public function copyToMediaLibraryFromRemote(RemoteFile $file, Media $media, ?string $type = null, ?string $targetFileName = null): void
    {
        $this->ensureMediaDirectoryExists($media, $type);

        parent::copyToMediaLibraryFromRemote($file, $media, $type, $targetFileName);
    }

    /**
     * Crée le répertoire du média s'il n'existe pas déjà.
     */
    protected function ensureMediaDirectoryExists(Media $media, ?string $type): void
    {
        $directory = $this->getMediaDirectory($media, $type);
        $diskName = (in_array($type ?? '', ['conversions', 'responsiveImages']))
            ? $media->conversions_disk
            : $media->disk;

        $this->filesystem->disk($diskName)->makeDirectory($directory);
    }
}
