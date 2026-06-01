<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Preset de filtres pour les tableaux TanStack.
 *
 * Stocke des snapshots de filtres par utilisateur, type d'entité et table.
 *
 * @property int $id
 * @property int $user_id
 * @property string $entity_type
 * @property string|null $table_id
 * @property string $name
 * @property string|null $search_text
 * @property array<array-key, mixed>|null $filters
 * @property int|null $limit
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereSearchText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereUserId($value)
 * @mixin \Eloquent
 */
class TableFilterPreset extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'entity_type',
        'table_id',
        'name',
        'search_text',
        'filters',
        'limit',
        'is_default',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'filters' => 'array',
        'limit' => 'integer',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
