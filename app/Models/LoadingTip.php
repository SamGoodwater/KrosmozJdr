<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoadingTipFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Astuce affichée en bas de l’écran de chargement du site.
 *
 * @property int $id
 * @property string $body
 * @property string|null $url
 * @property bool $featured
 * @property bool $is_active
 * @property int $duration_seconds Durée d’affichage lisible (hors fondu), 2–30 s
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static LoadingTipFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoadingTip whereUrl($value)
 * @mixin \Eloquent
 */
class LoadingTip extends Model
{
    /** @use HasFactory<LoadingTipFactory> */
    use HasFactory;

    public const DEFAULT_DURATION_SECONDS = 8;

    public const MIN_DURATION_SECONDS = 2;

    public const MAX_DURATION_SECONDS = 30;

    protected $fillable = [
        'body',
        'url',
        'featured',
        'is_active',
        'duration_seconds',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'duration_seconds' => self::DEFAULT_DURATION_SECONDS,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'duration_seconds' => 'integer',
        ];
    }
}
