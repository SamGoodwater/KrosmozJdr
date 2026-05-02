<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\Breed;
use App\Models\Entity\Spell;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Validation de la synchronisation des sorts d'une classe avec emplacements (pivot).
 *
 * @example
 *   'spells' => [
 *       12 => ['character_level' => 1, 'slot_index' => 1, 'choice_order' => 0],
 *   ]
 */
class UpdateBreedSpellsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $breed = $this->route('breed');

        return $breed instanceof Breed && $this->user()?->can('update', $breed) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'spells' => ['present', 'array'],
            'spells.*' => ['array'],
            'spells.*.character_level' => ['required', 'integer', 'min:1', 'max:200'],
            'spells.*.slot_index' => ['required', 'integer', 'min:1', 'max:50'],
            'spells.*.choice_order' => ['required', 'integer', 'min:0', 'max:255'],
        ];
    }

    /**
     * Vérifie que chaque clé de sorts correspond à un spell_id existant.
     *
     * @param  \Closure(Validator): void  $callback
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $spells = $this->input('spells', []);
            if (! is_array($spells)) {
                return;
            }
            foreach (array_keys($spells) as $spellId) {
                if (! is_numeric($spellId) || (int) $spellId < 1) {
                    $validator->errors()->add('spells', 'Chaque entrée doit être indexée par un identifiant de sort valide.');

                    return;
                }
                if (! Spell::query()->whereKey((int) $spellId)->exists()) {
                    $validator->errors()->add('spells', "Le sort #{$spellId} n'existe pas.");
                }
            }
        });
    }

    /**
     * @return array<int, array{character_level: int, slot_index: int, choice_order: int}>
     */
    public function validatedSpellsSyncPayload(): array
    {
        /** @var array<string|int, array<string, mixed>> $spells */
        $spells = $this->validated()['spells'] ?? [];
        $out = [];
        foreach ($spells as $spellId => $row) {
            $id = (int) $spellId;
            $out[$id] = [
                'character_level' => (int) $row['character_level'],
                'slot_index' => (int) $row['slot_index'],
                'choice_order' => (int) $row['choice_order'],
            ];
        }

        return $out;
    }
}
