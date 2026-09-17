<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Lance `project:review` depuis la file d’attente (options du rapport Markdown).
 *
 * @example run_all uniquement ou combinaisons partielles.
 */
class StoreProjectReviewWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user instanceof User && $user->isInteractiveSuperAdmin();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'run_all' => ['sometimes', 'boolean'],
            'pint' => ['sometimes', 'boolean'],
            'tests' => ['sometimes', 'boolean'],
            'test_back' => ['sometimes', 'boolean'],
            'test_front' => ['sometimes', 'boolean'],
            'phpstan' => ['sometimes', 'boolean'],
            'eslint' => ['sometimes', 'boolean'],
            'security' => ['sometimes', 'boolean'],
            'docs' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (array_keys($this->rules()) as $key) {
            if ($this->has($key)) {
                $this->merge([$key => filter_var($this->input($key), FILTER_VALIDATE_BOOLEAN)]);
            }
        }
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->boolean('run_all')) {
                    return;
                }
                $anyPartial = collect([
                    'pint', 'tests', 'test_back', 'test_front',
                    'phpstan', 'eslint', 'security', 'docs',
                ])->contains(fn ($k): bool => $this->boolean($k));

                if (! $anyPartial) {
                    $validator->errors()->add('scope', 'Sélectionne « Tout le périmètre » ou au moins une étape.');
                }
            },
        ];
    }

    /** @return array<string, mixed> */
    public function artisanArguments(): array
    {
        $out = [];

        if ($this->boolean('run_all')) {
            $out['--all'] = true;

            return $out;
        }

        $map = [
            'pint' => '--pint',
            'tests' => '--tests',
            'test_back' => '--test-back',
            'test_front' => '--test-front',
            'phpstan' => '--phpstan',
            'eslint' => '--eslint',
            'security' => '--security',
            'docs' => '--docs',
        ];

        foreach ($map as $input => $flag) {
            if ($this->boolean($input)) {
                $out[$flag] = true;
            }
        }

        return $out;
    }
}
