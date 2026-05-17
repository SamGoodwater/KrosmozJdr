<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot breed_spell : emplacement de sort (niveau PJ, slot, ordre des choix).
 *
 * @property int $breed_id
 * @property int $spell_id
 * @property int $character_level
 * @property int $slot_index
 * @property int $choice_order
 * @property int $id
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedSpellPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedSpellPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedSpellPivot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedSpellPivot whereBreedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedSpellPivot whereCharacterLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedSpellPivot whereChoiceOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedSpellPivot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedSpellPivot whereSlotIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BreedSpellPivot whereSpellId($value)
 *
 * @mixin \Eloquent
 */
class BreedSpellPivot extends Pivot
{
    /** @var bool La table pivot possède une clé `id` auto-incrémentée. */
    public $incrementing = true;

    protected $table = 'breed_spell';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'breed_id',
        'spell_id',
        'character_level',
        'slot_index',
        'choice_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'character_level' => 'integer',
            'slot_index' => 'integer',
            'choice_order' => 'integer',
        ];
    }
}
