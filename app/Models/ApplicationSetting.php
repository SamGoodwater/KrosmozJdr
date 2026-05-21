<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Paramètres applicatifs clé → JSON (ex. matrice « Gérer l’affichage »).
 *
 * @property int $id
 * @property string $key
 * @property array<string, mixed> $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationSetting whereValue($value)
 *
 * @mixin \Eloquent
 */
class ApplicationSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }
}
