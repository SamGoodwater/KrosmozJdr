<?php

declare(strict_types=1);

namespace App\Http\Requests\Entity;

use App\Models\Entity\Npc;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNpcCreatureTraitsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $npc = $this->route('npc');

        return $npc instanceof Npc && $this->user()?->can('update', $npc) === true;
    }

    /**
     * @return array<string, mixed>
     */
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
