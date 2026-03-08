<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataSubjectRequest extends Model
{
    use HasFactory;

    public const TYPE_EXPORT = 'export';
    public const TYPE_ERASURE = 'erasure';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'requested_at',
        'confirmed_at',
        'processed_at',
        'expires_at',
        'meta',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'processed_at' => 'datetime',
        'expires_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

