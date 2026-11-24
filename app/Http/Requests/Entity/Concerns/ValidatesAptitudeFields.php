<?php

namespace App\Http\Requests\Entity\Concerns;

trait ValidatesAptitudeFields
{
    /**
     * @return array<string, mixed>
     */
    protected function aptitudeFieldRules(bool $creating): array
    {
        $name = $creating
            ? ['required', 'string', 'max:255']
            : ['sometimes', 'required', 'string', 'max:255'];

        return [
            'name' => $name,
            'description' => ['nullable', 'string', 'max:5000'],
            'effect' => ['nullable', 'string', 'max:20000'],
            'reuse' => ['nullable', 'string', 'max:1000'],
            'duration' => ['nullable', 'string', 'max:1000'],
            'range' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
