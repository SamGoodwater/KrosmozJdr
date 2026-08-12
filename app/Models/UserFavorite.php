<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Favori utilisateur (fiche catalogue / page / section).
 *
 * @property int $id
 * @property int $user_id
 * @property string $entity_type
 * @property int $entity_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @example UserFavorite::query()->where('user_id', $id)->get();
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorite whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorite whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorite whereUserId($value)
 * @mixin \Eloquent
 */
class UserFavorite extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'entity_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
