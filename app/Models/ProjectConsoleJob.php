<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Suivi d’une commande Artisan lancée depuis l’admin (review, clear, deps, backup, sync).
 *
 * @property string $id
 * @property string $domain
 * @property string $status
 * @property int $progress
 * @property string|null $progress_label
 * @property string $command
 * @property string|null $page_url
 * @property string $output
 * @property string|null $error
 * @property int|null $exit_code
 * @property int|null $triggered_by
 * @property string|null $notification_id
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 */
class ProjectConsoleJob extends Model
{
    public const STATUS_QUEUED = 'queued';

    public const STATUS_RUNNING = 'running';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    protected $table = 'project_console_jobs';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'domain',
        'status',
        'progress',
        'progress_label',
        'command',
        'page_url',
        'output',
        'error',
        'exit_code',
        'triggered_by',
        'notification_id',
        'started_at',
        'finished_at',
    ];

    protected $attributes = [
        'output' => '',
        'progress' => 0,
        'status' => self::STATUS_QUEUED,
    ];

    protected $casts = [
        'progress' => 'integer',
        'exit_code' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $job): void {
            if (! $job->id) {
                $job->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_QUEUED, self::STATUS_RUNNING], true);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [self::STATUS_SUCCESS, self::STATUS_FAILED], true);
    }

    public static function hasActive(string $domain): bool
    {
        return self::query()
            ->where('domain', $domain)
            ->whereIn('status', [self::STATUS_QUEUED, self::STATUS_RUNNING])
            ->exists();
    }

    public static function latestForDomain(string $domain): ?self
    {
        return self::query()
            ->where('domain', $domain)
            ->latest('created_at')
            ->first();
    }

    /**
     * @return array{
     *   id: string,
     *   domain: string,
     *   status: string,
     *   progress: int,
     *   progress_label: string|null,
     *   command: string,
     *   output: string,
     *   error: string|null,
     *   exit_code: int|null,
     *   started_at: string|null,
     *   finished_at: string|null,
     *   created_at: string|null
     * }
     */
    public function toStatusPayload(): array
    {
        return [
            'id' => $this->id,
            'domain' => $this->domain,
            'status' => $this->status,
            'progress' => (int) $this->progress,
            'progress_label' => $this->progress_label,
            'command' => $this->command,
            'output' => (string) $this->output,
            'error' => $this->error,
            'exit_code' => $this->exit_code,
            'started_at' => $this->started_at?->toIso8601String(),
            'finished_at' => $this->finished_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
