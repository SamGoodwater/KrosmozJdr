<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Modèle placeholder pour les uploads d'images d'entités sans entité cible (ex. bulk).
 * Un média est attaché à cette instance ; l'URL retournée peut être affectée au champ
 * image de plusieurs entités (string). Nettoyage des anciennes lignes à prévoir (job).
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class EntityImageUpload extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections('images')
            ->width(368)
            ->height(232)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('webp')
            ->performOnCollections('images')
            ->format('webp')
            ->nonQueued();
    }
}
