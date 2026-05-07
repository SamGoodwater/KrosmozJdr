<?php

namespace App\Models\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Association voix élémentaire → orientation de classe (icône breed_orientations).
 *
 * @property int $id
 * @property int $breed_id
 * @property string $element air|earth|fire|water
 * @property string $orientation_key clef fichier (sans extension)
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Breed $breed
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedElementOrientation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedElementOrientation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedElementOrientation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedElementOrientation whereBreedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedElementOrientation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedElementOrientation whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedElementOrientation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedElementOrientation whereOrientationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedElementOrientation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BreedElementOrientation extends Model
{
    public const ELEMENT_AIR = 'air';

    public const ELEMENT_EARTH = 'earth';

    public const ELEMENT_FIRE = 'fire';

    public const ELEMENT_WATER = 'water';

    /** @var list<string> */
    public const ELEMENTS = [
        self::ELEMENT_AIR,
        self::ELEMENT_EARTH,
        self::ELEMENT_FIRE,
        self::ELEMENT_WATER,
    ];

    protected $table = 'breed_element_orientations';

    protected $fillable = [
        'breed_id',
        'element',
        'orientation_key',
    ];

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }
}
