<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\Creature;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCreatureCreatureTraitsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $creature = $this->route('creature');

        return $creature instanceof Creature && $this->user()?->can('update', $creature) === true;
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
