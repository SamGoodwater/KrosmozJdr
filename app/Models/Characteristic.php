<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasMediaCustomNaming;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Caractéristique générale : propriétés communes et id unique.
 * Une ligne = une caractéristique (ex. PA créature, PA sort, PA objet = 3 lignes).
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $short_name
 * @property string|null $helper
 * @property string|null $descriptions
 * @property string|null $icon
 * @property string|null $color
 * @property string|null $unit
 * @property string $type
 * @property int $sort_order
 */
class Characteristic extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasMediaCustomNaming;

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/characteristics';

    /** Motif de nommage pour la collection icons (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_ICONS = '[key]';

    protected $table = 'characteristics';

    /** @var list<string> */
    protected $fillable = [
        'key',
        'name',
        'short_name',
        'helper',
        'descriptions',
        'icon',
        'color',
        'unit',
        'type',
        'sort_order',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function creatureRows(): HasMany
    {
        return $this->hasMany(CharacteristicCreature::class, 'characteristic_id');
    }

    public function objectRows(): HasMany
    {
        return $this->hasMany(CharacteristicObject::class, 'characteristic_id');
    }

    public function spellRows(): HasMany
    {
        return $this->hasMany(CharacteristicSpell::class, 'characteristic_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icons')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->performOnCollections('icons')
            ->format('webp')
            ->nonQueued();
    }
}
