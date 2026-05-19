<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $data_subject_request_id
 * @property string $status
 * @property string $path
 * @property string|null $checksum
 * @property Carbon|null $expires_at
 * @property Carbon|null $downloaded_at
 * @property array<array-key, mixed>|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DataSubjectRequest|null $dataSubjectRequest
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereChecksum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereDataSubjectRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereDownloadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereUserId($value)
 * @mixin \Eloquent
 */
class PrivacyExport extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_READY = 'ready';

    public const STATUS_FAILED = 'failed';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'data_subject_request_id',
        'status',
        'path',
        'checksum',
        'expires_at',
        'downloaded_at',
        'meta',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataSubjectRequest(): BelongsTo
    {
        return $this->belongsTo(DataSubjectRequest::class);
    }
}
