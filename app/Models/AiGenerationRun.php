<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Journal d’un paquet de conversion IA (tokens réels, modèle, cible).
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string $entity_type
 * @property int|null $entity_id
 * @property array<int, int>|null $related_ids
 * @property string|null $model
 * @property int $input_tokens
 * @property int $output_tokens
 * @property int $cache_read_tokens
 * @property string $status
 * @property string|null $error
 * @property string|null $prompt_version
 * @property Carbon|null $ai_generated_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereAiGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereCacheReadTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereInputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereOutputTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun wherePromptVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereRelatedIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiGenerationRun whereUserId($value)
 * @mixin \Eloquent
 */
class AiGenerationRun extends Model
{
    public const STATUS_QUEUED = 'queued';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'related_ids',
        'model',
        'input_tokens',
        'output_tokens',
        'cache_read_tokens',
        'status',
        'error',
        'prompt_version',
        'ai_generated_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'related_ids' => 'array',
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'cache_read_tokens' => 'integer',
        'ai_generated_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
