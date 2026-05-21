<?php

declare(strict_types=1);

namespace App\Models\Scrapping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Cible d'une règle de mapping : un couple (model, field) Krosmoz.
 *
 * Une règle peut avoir plusieurs cibles (ex. item → resources, consumables, items).
 *
 * @property int $id
 * @property int $scrapping_entity_mapping_id
 * @property string $target_model
 * @property string $target_field
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ScrappingEntityMapping $scrappingEntityMapping
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereScrappingEntityMappingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereTargetField($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereTargetModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ScrappingEntityMappingTarget extends Model
{
    protected $table = 'scrapping_entity_mapping_targets';

    /** @var list<string> */
    protected $fillable = [
        'scrapping_entity_mapping_id',
        'target_model',
        'target_field',
        'sort_order',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function scrappingEntityMapping(): BelongsTo
    {
        return $this->belongsTo(ScrappingEntityMapping::class);
    }

    /**
     * Retourne le couple (model, field) au format attendu par ConversionService et par les vues (liste, panneau caractéristique).
     * Une seule représentation pour éviter la duplication dans ScrappingMappingService et CharacteristicController.
     *
     * @return array{model: string, field: string}
     */
    public function toConversionPair(): array
    {
        return [
            'model' => $this->target_model,
            'field' => $this->target_field,
        ];
    }

    /**
     * Retourne la cible au format attendu par le formulaire d’édition (admin mappings).
     *
     * @return array{id: int, target_model: string, target_field: string, sort_order: int}
     */
    public function toResponseArray(): array
    {
        return [
            'id' => $this->id,
            'target_model' => $this->target_model,
            'target_field' => $this->target_field,
            'sort_order' => $this->sort_order,
        ];
    }
}
