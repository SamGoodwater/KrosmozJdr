<?php

namespace App\Models\Entity;

use Database\Factories\Entity\LanguageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Langue (référentiel) — associable aux classes, monstres, spécialisations, etc.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color Hex #RRGGBB
 *
 * @method static LanguageFactory factory($count = null, $state = [])
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
