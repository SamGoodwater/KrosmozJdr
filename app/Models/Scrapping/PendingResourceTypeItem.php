<?php

namespace App\Models\Scrapping;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle de stockage des items DofusDB "en attente" pour un typeId non encore autorisé.
 *
 * @example
 * PendingResourceTypeItem::create([
 *   'dofusdb_type_id' => 99,
 *   'dofusdb_item_id' => 12345,
 *   'context' => 'recipe',
 * ]);
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


