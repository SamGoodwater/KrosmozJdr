<?php

namespace App\Models\Entity;

use App\Models\Concerns\HasEntityImageMedia;
use App\Models\Concerns\VisibleToViewer;
use App\Models\EffectUsage;
use App\Models\ObjectEffect;
use App\Models\Type\ItemType;
use App\Models\User;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string|null $level
 * @property string|null $description
 * @property string|null $effect
 * @property string|null $bonus
 * @property string|null $recipe
 * @property int|null $price_calculated
 * @property int|null $price_custom
 * @property string|null $price Total kamas affiché (entier, synchronisé depuis calculé + personnalisé)
 * @property int $rarity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property Carbon|null $deleted_at
 * @property int|null $item_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read ItemType|null $itemType
 * @property-read Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read Collection<int, resource> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Database\Factories\Entity\ItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereItemTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRecipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item withoutTrashed()
 * @property-read Collection<int, EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, ObjectEffect> $objectEffects
 * @property-read int|null $object_effects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePriceCalculated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePriceCustom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item visibleToUser(?\App\Models\User $user)
 * @mixin \Eloquent
 */
class Item extends Model implements HasMedia
{
    /** @use HasFactory<ItemFactory> */
    use HasEntityImageMedia, HasFactory, SoftDeletes, VisibleToViewer;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    /** Répertoire Media Library pour ce modèle. */
    public const MEDIA_PATH = 'images/entity/items';

    /** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */
    public const MEDIA_FILE_PATTERN_IMAGES = 'image-[id]-[name]';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'official_id',
        'dofusdb_id',
        'name',
        'level',
        'description',
        'effect',
        'bonus',
        'recipe',
        'price_calculated',
        'price_custom',
        'rarity',
        'dofus_version',
        'state',
        'read_level',
        'write_level',
        'image',
        'auto_update',
        'item_type_id',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_calculated' => 'integer',
        'price_custom' => 'integer',
        'rarity' => 'integer',
        'read_level' => 'integer',
        'write_level' => 'integer',
        'auto_update' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Item $item): void {
            $item->price = (string) $item->totalPriceKamas();
        });
    }

    /**
     * Total kamas (entier, plancher à 0) : part calculée + part personnalisée (peut être négative).
     */
    public function totalPriceKamas(): int
    {
        $calc = $this->price_calculated !== null ? (int) $this->price_calculated : 0;
        $custom = $this->price_custom !== null ? (int) $this->price_custom : 0;

        return max(0, (int) round($calc + $custom));
    }

    /**
     * Prix à exposer dans les vues lecture (null si total ≤ 0).
     */
    public function displayPriceKamas(): ?int
    {
        $total = $this->totalPriceKamas();

        return $total > 0 ? $total : null;
    }

    /**
     * Get the user that created the item.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the type of the item.
     */
    public function itemType()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

    /**
     * Les ressources nécessaires à cet objet.
     */
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'item_resource')->withPivot('quantity');
    }

    /**
     * Les panoplies associées à cet objet.
     */
    public function panoplies()
    {
        return $this->belongsToMany(Panoply::class, 'item_panoply');
    }

    /**
     * Les scénarios associés à cet objet.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'item_scenario');
    }

    /**
     * Les campagnes associées à cet objet.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'item_campaign');
    }

    /**
     * Les hotels de vente associées à cet objet.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'item_shop')->withPivot('quantity', 'price', 'comment');
    }

    /**
     * Usages d'effets unifiés (effect_usage) pour cet item.
     */
    public function effectUsages()
    {
        return $this->morphMany(EffectUsage::class, 'entity');
    }

    /**
     * Effets d’objet structurés (action + caractéristique ou monstre + valeur).
     */
    public function objectEffects()
    {
        return $this->morphMany(ObjectEffect::class, 'object_effectable');
    }
}
