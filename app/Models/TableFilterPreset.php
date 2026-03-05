<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Preset de filtres pour les tableaux TanStack.
 *
 * Stocke des snapshots de filtres par utilisateur, type d'entité et table.
 */
class TableFilterPreset extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'entity_type',
        'table_id',
        'name',
        'search_text',
        'filters',
        'limit',
        'is_default',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'filters' => 'array',
        'limit' => 'integer',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

