<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Message appartenant à une conversation de feedback.
 *
 * @example $message->attachmentUrl();
 * @property int $id
 * @property int $feedback_thread_id
 * @property int|null $author_id
 * @property string $author_role
 * @property string $body
 * @property string|null $attachment_path
 * @property string|null $attachment_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $author
 * @property-read string|null $attachment_url
 * @property-read \App\Models\FeedbackThread $thread
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage whereAttachmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage whereAttachmentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage whereAuthorRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage whereFeedbackThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeedbackMessage extends Model
{
    use HasFactory;

    public const AUTHOR_USER = 'user';

    public const AUTHOR_STAFF = 'staff';

    protected $fillable = [
        'feedback_thread_id',
        'author_id',
        'author_role',
        'body',
        'attachment_path',
        'attachment_name',
    ];

    protected $appends = ['attachment_url'];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(FeedbackThread::class, 'feedback_thread_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path
            ? Storage::disk('public')->url($this->attachment_path)
            : null;
    }
}
