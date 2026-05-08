<?php

namespace App\Http\Requests\Entity\Concerns;

/**
 * Règles et normalisation pour synchroniser des traits avec niveau (pivot breed / specialization).
 *
 * @mixin \Illuminate\Foundation\Http\FormRequest
 */
trait ValidatesLeveledCreatureTraitSync
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'creature_traits' => ['nullable', 'array'],
            'creature_traits.*.id' => ['required', 'integer', 'exists:creature_traits,id'],
            'creature_traits.*.level' => ['nullable', 'integer', 'min:1', 'max:200'],
        ];
    }

    /**
     * @return array<int, array{level: int}>
     */
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
