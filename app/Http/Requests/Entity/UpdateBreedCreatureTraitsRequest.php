<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\Breed;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBreedCreatureTraitsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $breed = $this->route('breed');

        return $breed instanceof Breed && $this->user()?->can('update', $breed) === true;
    }

    public function rules(): array
    {
        return [
            'creature_traits' => ['nullable', 'array'],
            'creature_traits.*.id' => ['required', 'integer', 'exists:creature_traits,id'],
            'creature_traits.*.level' => ['nullable', 'integer', 'min:1', 'max:200'],
        ];
    }

    /** @return array<int, array{level: int}> */
    public function validatedCreatureTraitSyncPayload(): array
    {
        $raw = $this->validated()['creature_traits'] ?? $this->input('creature_traits', []);

        $sync = [];
        foreach (is_array($raw) ? $raw : [] as $row) {
            if (! is_array($row) || ! isset($row['id'])) {
                continue;
            }

            $id = (int) $row['id'];
            if ($id <= 0) {
                continue;
            }

            $sync[$id] = [
                'level' => max(1, (int) ($row['level'] ?? 1)),
            ];
        }

        return $sync;
    }
}
