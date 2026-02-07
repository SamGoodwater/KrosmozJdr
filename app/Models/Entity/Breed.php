<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Entity\Npc;
use App\Models\Entity\Spell;
use App\Models\Concerns\HasEntityImageMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Entité Breed (affichée « Classe » côté utilisateur).
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description_fast
 * @property string|null $description
 * @property string|null $life
 * @property string|null $life_dice
 * @property string|null $specificity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property string|null $icon
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Database\Factories\Entity\BreedFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed query()
 * @mixin \Eloquent
 */
class Breed extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\Entity\BreedFactory> */
    use HasFactory, SoftDeletes, HasEntityImageMedia;

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/breeds';

    /** Motif de nommage pour la collection icons (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_ICONS = 'icon-[id]-[name]';
    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    protected $table = 'breeds';

    public const STATE_RAW = 'raw';
    public const STATE_DRAFT = 'draft';
    public const STATE_PLAYABLE = 'playable';
    public const STATE_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'name',
        'description_fast',
        'description',
        'life',
        'life_dice',
        'specificity',
        'dofus_version',
        'state',
        'read_level',
        'write_level',
        'image',
        'icon',
        'auto_update',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_level' => 'integer',
        'write_level' => 'integer',
        'auto_update' => 'boolean',
    ];

    /**
     * Get the user that created the breed.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les PNJ associés à cette breed.
     */
    public function npcs()
    {
        return $this->hasMany(Npc::class, 'breed_id');
    }

    /**
     * Les sorts associés à cette breed.
     */
    public function spells()
    {
        return $this->belongsToMany(Spell::class, 'breed_spell', 'breed_id', 'spell_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->singleFile();
        $this->addMediaCollection('icons')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->registerEntityImageMediaConversions($media);
        $this->addMediaConversion('webp')
            ->performOnCollections('icons')
            ->format('webp')
            ->nonQueued();
    }
}
