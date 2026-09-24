<?php

namespace App\Models\Entity;

use App\Models\Concerns\VisibleToViewer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $bonus
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read int|null $computed_level Plus haut niveau numérique des pièces chargées.
 * @property-read Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Database\Factories\Entity\PanoplyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply withoutTrashed()
 * @property string|null $dofusdb_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply visibleToUser(?\App\Models\User $user)
 * @mixin \Eloquent
 */
class Panoply extends Model
{
    /** @use HasFactory<\\Database\\Factories\\PanoplyFactory> */
    use HasFactory, SoftDeletes, VisibleToViewer;

    public const STATE_RAW = 'raw';

    public const STATE_DRAFT = 'draft';

    public const STATE_AUTO = 'auto';

    public const STATE_PLAYABLE = 'playable';

    public const STATE_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'bonus',
        'state',
        'read_level',
        'write_level',
        'created_by',
        'dofusdb_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_level' => 'integer',
        'write_level' => 'integer',
    ];

    /**
     * Get the user that created the panoply.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Les objets de cette panoplie.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_panoply');
    }

    /**
     * Plus haut niveau numérique parmi une liste de pièces (`items.level` est une chaîne).
     *
     * @param  iterable<int, mixed>  $items
     *
     * @example Panoply::maxLevelFromItems([['level' => '20'], ['level' => '100']]); // 100
     */
    public static function maxLevelFromItems(iterable $items): ?int
    {
        $max = null;
        foreach ($items as $item) {
            $raw = null;
            if (is_object($item)) {
                $raw = $item->level ?? null;
            } elseif (is_array($item)) {
                $raw = $item['level'] ?? null;
            }
            if ($raw === null || $raw === '') {
                continue;
            }
            if (! is_numeric($raw)) {
                continue;
            }
            $n = (int) $raw;
            $max = $max === null ? $n : max($max, $n);
        }

        return $max;
    }

    /**
     * Niveau du set = max des pièces déjà eager-loadées (pas de N+1).
     *
     * @example $panoply->load('items'); $panoply->computedLevel();
     */
    public function computedLevel(): ?int
    {
        if (! $this->relationLoaded('items')) {
            return null;
        }

        return self::maxLevelFromItems($this->items);
    }

    /**
     * Sous-requête corrélée : MAX(CAST(items.level AS SIGNED)) des pièces visibles.
     *
     * @param  Builder<Panoply>  $query
     *
     * @example Panoply::orderByComputedLevel($query, 'asc', $request->user());
     */
    public static function orderByComputedLevel(Builder $query, string $direction, ?User $viewer = null): void
    {
        $dir = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';
        [$sql, $bindings] = self::maxVisibleItemLevelSql($viewer);
        $query->orderByRaw("{$sql} IS NULL, {$sql} {$dir}", array_merge($bindings, $bindings));
    }

    /**
     * SQL + bindings de la sous-requête de niveau dérivé (filtre / tri).
     *
     * @return array{0: string, 1: list<mixed>}
     *
     * @example [$sql, $bindings] = Panoply::maxVisibleItemLevelSql($viewer);
     */
    public static function maxVisibleItemLevelSql(?User $viewer = null): array
    {
        $sub = self::maxVisibleItemLevelQuery($viewer);

        return ['('.$sub->toSql().')', $sub->getBindings()];
    }

    /**
     * @return Builder<Item>
     */
    public static function maxVisibleItemLevelQuery(?User $viewer = null): Builder
    {
        return Item::query()
            ->selectRaw('MAX(CAST(`items`.`level` AS SIGNED))')
            ->whereNotNull('items.level')
            ->where('items.level', '!=', '')
            ->visibleToUser($viewer)
            ->whereExists(function ($q): void {
                $q->selectRaw('1')
                    ->from('item_panoply')
                    ->whereColumn('item_panoply.item_id', 'items.id')
                    ->whereColumn('item_panoply.panoply_id', 'panoplies.id');
            });
    }

    /**
     * Les PNJ associés à cette panoplie.
     */
    public function npcs()
    {
        return $this->belongsToMany(Npc::class, 'npc_panoply');
    }

    /**
     * Les hotels de vente associées à cette panoplie.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'panoply_shop');
    }

    /**
     * Les scénarios associés à cette panoplie.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'scenario_panoply');
    }

    /**
     * Les campagnes associées à cette panoplie.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_panoply');
    }
}
