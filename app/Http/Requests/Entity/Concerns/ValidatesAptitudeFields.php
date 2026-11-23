<?php

namespace App\Http\Requests\Entity\Concerns;

use App\Models\Entity\Aptitude;
use Illuminate\Validation\Rule;

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
            'reuse_type' => ['sometimes', 'string', Rule::in([
                Aptitude::REUSE_UNLIMITED,
                Aptitude::REUSE_SHORT_REST,
                Aptitude::REUSE_LONG_REST,
                Aptitude::REUSE_TIMED,
            ])],
            'reuse_amount' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'reuse_unit' => ['nullable', 'string', Rule::in(['minute', 'hour', 'day'])],
            'duration_type' => ['sometimes', 'string', Rule::in([
                Aptitude::DURATION_INSTANT,
                Aptitude::DURATION_TIMED,
                Aptitude::DURATION_PERMANENT,
                Aptitude::DURATION_WHILE_CONDITION,
            ])],
            'duration_amount' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'duration_unit' => ['nullable', 'string', Rule::in(['minute', 'hour', 'day', 'round', 'scene'])],
            'duration_note' => ['nullable', 'string', 'max:500'],
            'range_type' => ['sometimes', 'string', Rule::in([
                Aptitude::RANGE_SELF,
                Aptitude::RANGE_DISTANCE,
                Aptitude::RANGE_SENSE,
            ])],
            'range_amount' => ['nullable', 'integer', 'min:1', 'max:99999'],
            'range_unit' => ['nullable', 'string', Rule::in(['m', 'km'])],
            'range_sense' => ['nullable', 'string', Rule::in(['hearing', 'sight', 'touch', 'custom'])],
            'range_note' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
