<?php

namespace App\Services\Media;

use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Section;

/**
 * PathGenerator qui lit le répertoire depuis la constante MEDIA_PATH du modèle.
 *
 * Si le modèle lié au média définit la constante MEDIA_PATH (ex: 'images/entity/breeds'),
 * les fichiers sont stockés sous {MEDIA_PATH}/{id_media}/. Sinon, comportement par défaut (id).
 *
 * Prend en compte le morphMap (model_type peut être un alias comme 'spell' au lieu du FQCN).
 *
 * Chaque média reste dans son propre dossier (id) pour éviter les conflits à la suppression.
 */
class ModelAwarePathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        $modelType = $media->model_type ?? '';
        $modelClass = is_string($modelType) ? (Relation::getMorphedModel($modelType) ?? $modelType) : $modelType;
        $prefix = config('media-library.prefix', '');

        // Sections: stockage demandé sous sections/{section_id}/{media_id}
        if ($modelClass === Section::class || $modelType === Section::class) {
            $sectionId = (int) ($media->model_id ?? 0);
            if ($sectionId > 0) {
                $path = "sections/{$sectionId}/{$media->getKey()}";
                return $prefix !== '' ? $prefix . '/' . $path : $path;
            }
        }

        if ($modelClass !== '' && is_string($modelClass) && class_exists($modelClass) && defined("{$modelClass}::MEDIA_PATH")) {
            $base = trim($modelClass::MEDIA_PATH, '/');
            $path = $base . '/' . $media->getKey();

            return $prefix !== '' ? $prefix . '/' . $path : $path;
        }

        return parent::getBasePath($media);
    }
}
