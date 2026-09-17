<?php

namespace App\Models\Scrapping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Modèle de stockage des items DofusDB "en attente" pour un typeId non encore autorisé.
 *
 * @example PendingResourceTypeItem::create([
 *   'dofusdb_type_id' => 99,
 *   'dofusdb_item_id' => 12345,
 *   'context' => 'recipe',
 * ]);
 * @property int $id
 * @property int $dofusdb_type_id
 * @property int $dofusdb_item_id
 * @property string $context
 * @property string|null $source_entity_type
 * @property int|null $source_entity_dofusdb_id
 * @property int|null $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereDofusdbItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereDofusdbTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereSourceEntityDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereSourceEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PendingResourceTypeItem extends Model
{
    protected $table = 'scrapping_pending_resource_type_items';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'dofusdb_type_id',
        'dofusdb_item_id',
        'context',
        'source_entity_type',
        'source_entity_dofusdb_id',
        'quantity',
    ];

    protected $casts = [
        'dofusdb_type_id' => 'integer',
        'dofusdb_item_id' => 'integer',
        'source_entity_dofusdb_id' => 'integer',
        'quantity' => 'integer',
    ];
}
