<?php

namespace App\Http\Requests\Entity\Concerns;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation/normalisation pour synchroniser une relation many-to-many avec pivot `level`.
 *
 * @mixin FormRequest
 */
trait ValidatesLeveledRelationSync
{
    abstract protected function relationInputKey(): string;

    abstract protected function relationEntityTable(): string;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $key = $this->relationInputKey();
        $table = $this->relationEntityTable();

        return [
            $key => ['nullable', 'array'],
            "{$key}.*.id" => ['required', 'integer', "exists:{$table},id"],
            "{$key}.*.level" => ['nullable', 'integer', 'min:1', 'max:200'],
        ];
    }

    /**
     * @return array<int, array{level: int}>
     */
    public function validatedLeveledSyncPayload(): array
    {
        $key = $this->relationInputKey();
        $raw = $this->validated()[$key] ?? $this->input($key, []);
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
