<?php

declare(strict_types=1);

namespace App\Models\Scrapping;

use App\Models\Characteristic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Règle de mapping : une clé logique (ex. level, name) pour une source+entité DofusDB.
 * Lie un chemin API (from_path) à une ou plusieurs cibles Krosmoz (model.field) avec formatters.
 *
 * @property int $id
 * @property string $source
 * @property string $entity
 * @property string $mapping_key
 * @property string $from_path
 * @property bool $from_lang_aware
 * @property int|null $characteristic_id
 * @property array|null $formatters
 * @property string|null $spell_level_aggregation first|max|min|last (agrégation multi spell-level)
 * @property int $sort_order
 *
 * @example
 * ScrappingEntityMapping::where('source', 'dofusdb')->where('entity', 'monster')->orderBy('sort_order')->get();
 */
class ScrappingEntityMapping extends Model
{
    protected $table = 'scrapping_entity_mappings';

    /** @var list<string> */
    protected $fillable = [
        'source',
        'entity',
        'mapping_key',
        'from_path',
        'from_lang_aware',
        'characteristic_id',
        'formatters',
        'spell_level_aggregation',
        'sort_order',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'from_lang_aware' => 'boolean',
        'formatters' => 'array',
        'sort_order' => 'integer',
    ];

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }

    /**
     * Caractéristiques liées via la table pivot (plusieurs caractéristiques par règle).
     *
     * @return BelongsToMany<Characteristic, $this>
     */
    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(
            Characteristic::class,
            'scrapping_entity_mapping_characteristic',
            'scrapping_entity_mapping_id',
            'characteristic_id'
        );
    }

    /** @return HasMany<ScrappingEntityMappingTarget, $this> */
    public function targets(): HasMany
    {
        return $this->hasMany(ScrappingEntityMappingTarget::class, 'scrapping_entity_mapping_id')->orderBy('sort_order');
    }

    /**
     * Cibles au format ConversionService / vues : liste de [model, field].
     * Utilise la relation targets (à charger avec with('targets') si besoin).
     *
     * @return list<array{model: string, field: string}>
     */
    public function getTargetsForConversion(): array
    {
        return $this->targets->map(fn (ScrappingEntityMappingTarget $t) => $t->toConversionPair())->values()->all();
    }

    /**
     * Résumé pour affichage (ex. panneau 3 caractéristique, liste sans formatters).
     * Une seule forme pour éviter de dupliquer la structure dans les contrôleurs.
     *
     * @return array{id: int, source: string, entity: string, mapping_key: string, from_path: string, targets: list<array{model: string, field: string}>}
     */
    public function toSummaryArray(): array
    {
        return [
            'id' => $this->id,
            'source' => $this->source,
            'entity' => $this->entity,
            'mapping_key' => $this->mapping_key,
            'from_path' => $this->from_path,
            'targets' => $this->getTargetsForConversion(),
        ];
    }
}
