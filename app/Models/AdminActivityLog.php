<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Journal admin des actions sensibles (entités, CMS, outils).
 *
 * @property int $id
 * @property string $domain
 * @property string $action
 * @property string $subject_type
 * @property int $subject_id
 * @property string|null $subject_label
 * @property int|null $actor_id
 * @property string $status
 * @property array<array-key, mixed>|null $properties
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $actor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereSubjectLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminActivityLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminActivityLog extends Model
{
    protected $fillable = [
        'domain',
        'action',
        'subject_type',
        'subject_id',
        'subject_label',
        'actor_id',
        'status',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
