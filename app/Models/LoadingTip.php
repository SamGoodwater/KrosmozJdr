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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static LoadingTipFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class LoadingTip extends Model
{
    /** @use HasFactory<LoadingTipFactory> */
    use HasFactory;

    protected $fillable = [
        'body',
        'url',
        'featured',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
