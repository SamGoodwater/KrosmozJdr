<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fil de conversation créé depuis un retour utilisateur connecté.
 *
 * @example $thread = FeedbackThread::query()->with('messages.author')->find(1);
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $status
 * @property string|null $url
 * @property string $subject_preview
 * @property \Illuminate\Support\Carbon|null $last_message_at
 * @property int $user_unread_count
 * @property int $staff_unread_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeedbackMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereLastMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereStaffUnreadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereSubjectPreview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackThread whereUserUnreadCount($value)
 * @mixin \Eloquent
 */
class FeedbackThread extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_AWAITING_USER = 'awaiting_user';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'url',
        'subject_preview',
        'last_message_at',
        'user_unread_count',
        'staff_unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'user_unread_count' => 'integer',
        'staff_unread_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(FeedbackMessage::class)->orderBy('created_at');
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }
}
