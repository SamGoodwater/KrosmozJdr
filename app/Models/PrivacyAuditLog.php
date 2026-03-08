<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Journal d'audit des actions RGPD (export, suppression).
 *
 * @property int $id
 * @property int|null $actor_id Utilisateur ayant effectué l'action
 * @property int|null $subject_user_id Utilisateur concerné par l'action
 * @property string $action Type d'action
 * @property array|null $context Contexte additionnel
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 */
class PrivacyAuditLog extends Model
{
    public const UPDATED_AT = null;

    public const ACTION_EXPORT_REQUESTED = 'export_requested';
    public const ACTION_EXPORT_DOWNLOADED = 'export_downloaded';
    public const ACTION_ERASURE_REQUESTED = 'erasure_requested';
    public const ACTION_ERASURE_CANCELLED = 'erasure_cancelled';
    public const ACTION_ERASURE_STARTED = 'erasure_started';
    public const ACTION_ERASURE_EXECUTED = 'erasure_executed';

    protected $fillable = [
        'actor_id',
        'subject_user_id',
        'action',
        'context',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function subjectUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subject_user_id');
    }

    public static function log(
        string $action,
        ?int $subjectUserId,
        ?int $actorId = null,
        ?array $context = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): self {
        return self::query()->create([
            'action' => $action,
            'subject_user_id' => $subjectUserId,
            'actor_id' => $actorId,
            'context' => $context,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }
}
