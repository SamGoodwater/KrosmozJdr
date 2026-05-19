<?php

namespace App\Models\Entity;

use Database\Factories\Entity\LanguageFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Langue (référentiel) — associable aux classes, monstres, spécialisations, etc.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color Hex #RRGGBB
 * @method static LanguageFactory factory($count = null, $state = [])
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Breed> $breeds
 * @property-read int|null $breeds_count
 * @property-read Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Language extends Model
{
    /** @use HasFactory<LanguageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    public function breeds(): BelongsToMany
    {
        return $this->belongsToMany(Breed::class, 'breed_language')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function monsters(): BelongsToMany
    {
        return $this->belongsToMany(Monster::class, 'monster_language')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
