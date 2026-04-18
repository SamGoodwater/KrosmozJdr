<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Modèle placeholder pour les uploads d'images d'entités sans entité cible (ex. bulk).
 *
 * Un média est attaché à cette instance ; l'URL retournée peut être affectée au champ
 * image de plusieurs entités (string). Nettoyage des anciennes lignes à prévoir (job).
 *
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload whereUpdatedAt($value)
 *
 * @mixin \Eloquent
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
