<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Réglages IA métier persistés (surcharge de `resources/ia/generation.json`).
 *
 * @property int $id
 * @property array<string, mixed> $payload
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IaGenerationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IaGenerationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IaGenerationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IaGenerationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IaGenerationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IaGenerationSetting wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IaGenerationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IaGenerationSetting whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class IaGenerationSetting extends Model
{
    protected $table = 'ia_generation_settings';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'payload',
        'updated_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
