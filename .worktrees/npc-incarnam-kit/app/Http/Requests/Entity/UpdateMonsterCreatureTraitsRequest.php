<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\Monster;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMonsterCreatureTraitsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $monster = $this->route('monster');

        return $monster instanceof Monster && $this->user()?->can('update', $monster) === true;
    }

    public function rules(): array
    {
        return [
            'creature_traits' => ['nullable', 'array'],
            'creature_traits.*' => ['integer', 'exists:creature_traits,id'],
        ];
    }

    /** @return list<int> */
    public function validatedCreatureTraitIds(): array
    {
        $raw = $this->validated()['creature_traits'] ?? $this->input('creature_traits', []);

        return array_values(array_unique(array_map(static fn ($id) => (int) $id, is_array($raw) ? $raw : [])));
    }
}
