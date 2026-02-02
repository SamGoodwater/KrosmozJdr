<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entity\Creature;
use App\Models\Entity\Breed;
use App\Models\Entity\Specialization;
use App\Models\Entity\Scenario;
use App\Models\Entity\Campaign;
use App\Models\Entity\Shop;
use App\Models\Entity\Panoply;

/**
 * 
 *
 * @property int $id
 * @property int|null $creature_id
 * @property string|null $story
 * @property string|null $historical
 * @property string|null $age
 * @property string|null $size
 * @property int|null $breed_id
 * @property int|null $specialization_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Breed|null $breed
 * @property-read Creature|null $creature
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Shop|null $shop
 * @property-read Specialization|null $specialization
 * @method static \Database\Factories\Entity\NpcFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereBreedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereCreatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereHistorical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSpecializationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Npc extends Model
{
    /** @use HasFactory<\\Database\\Factories\\NpcFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'creature_id',
        'story',
        'historical',
        'age',
        'size',
        'breed_id',
        'specialization_id',
    ];

    /**
     * Get the creature associated with the NPC.
     */
    public function creature()
    {
        return $this->belongsTo(Creature::class, 'creature_id');
    }

    /**
     * Les panoplies associées à ce PNJ.
     */
    public function panoplies()
    {
        return $this->belongsToMany(Panoply::class, 'npc_panoply');
    }

    /**
     * Get the breed (affichée « Classe ») associated with the NPC.
     */
    public function breed()
    {
        return $this->belongsTo(Breed::class, 'breed_id');
    }

    /**
     * Get the specialization associated with the NPC.
     */
    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id');
    }

    /**
     * Les scénarios associés à ce PNJ.
     */
    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class, 'npc_scenario');
    }

    /**
     * Les campagnes associées à ce PNJ.
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'npc_campaign');
    }

    /**
     * La boutique associée à ce PNJ.
     */
    public function shop()
    {
        return $this->hasOne(Shop::class, 'npc_id');
    }
}
