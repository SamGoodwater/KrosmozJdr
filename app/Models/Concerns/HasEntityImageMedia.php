<?php

namespace App\Models\Concerns;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Trait pour les entités avec une image principale (collection "images", singleFile).
 * Utilise Media Library partout ; la colonne image est synchronisée après ajout de média.
 * Optionnel : constantes MEDIA_PATH et MEDIA_FILE_PATTERN_* (voir HasMediaCustomNaming).
 */
trait HasEntityImageMedia
{
    use InteractsWithMedia;
    use HasMediaCustomNaming;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->registerEntityImageMediaConversions($media);
    }

    protected function registerEntityImageMediaConversions(?Media $media = null): void
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
