<?php

namespace App\Services\Media;

use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * PathGenerator qui lit le répertoire depuis la constante MEDIA_PATH du modèle.
 *
 * Si le modèle lié au média définit la constante MEDIA_PATH (ex: 'images/entity/breeds'),
 * les fichiers sont stockés sous {MEDIA_PATH}/{id_media}/. Sinon, comportement par défaut (id).
 *
 * Chaque média reste dans son propre dossier (id) pour éviter les conflits à la suppression.
 */
class ModelAwarePathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        $modelClass = $media->model_type ?? '';

        if ($modelClass !== '' && defined("{$modelClass}::MEDIA_PATH")) {
            $prefix = config('media-library.prefix', '');
            $base = trim($modelClass::MEDIA_PATH, '/');
            $path = $base . '/' . $media->getKey();

            return $prefix !== '' ? $prefix . '/' . $path : $path;
        }

        return parent::getBasePath($media);
    }
}
