<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read User $user
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
