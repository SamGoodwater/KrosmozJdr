<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\Monster;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Synchronisation des langues liées à un monstre (pivot monster_language + sort_order).
 */
class UpdateMonsterLanguagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $monster = $this->route('monster');

        return $monster instanceof Monster && $this->user()?->can('update', $monster) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'languages' => ['nullable', 'array'],
            'languages.*' => ['integer', 'exists:languages,id'],
        ];
    }

    /**
     * @return list<int>
     */
    public function validatedLanguageIdsOrdered(): array
    {
        $raw = $this->validated()['languages'] ?? $this->input('languages', []);

        $ids = array_map(static fn ($id) => (int) $id, is_array($raw) ? $raw : []);
        $out = [];
        $seen = [];
        foreach ($ids as $id) {
            if ($id < 1 || isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            $out[] = $id;
        }

        return $out;
    }
}
