<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Entrée en attente pour envoi en digest (quotidien, hebdo, mensuel).
 *
 * Le payload est stocké en JSON ; NotificationService::pushToDigestQueue le normalise
 * (Carbon, Enum, etc.) avant enregistrement.
 *
 * @property int $id
 * @property int $user_id
 * @property string $notification_type
 * @property string $frequency
 * @property array $payload
 * @property Carbon $created_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereNotificationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereUserId($value)
 * @mixin \Eloquent
 */
class NotificationDigestQueue extends Model
{
    public $timestamps = false;

    protected $table = 'notification_digest_queue';

    protected $fillable = [
        'user_id',
        'notification_type',
        'frequency',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
