<?php

namespace App\Http\Requests\Entity;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePanoplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $panoply = $this->route('panoply');
        if (! $user || ! $panoply) {
            return false;
        }

        return $user->can('update', $panoply);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'bonus' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }
                    json_decode((string) $value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $fail('Les bonus doivent être un JSON valide.');
                    }
                },
            ],
            'state' => ['nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $bonus = $this->input('bonus');
        if (is_array($bonus)) {
            $this->merge(['bonus' => json_encode($bonus, JSON_UNESCAPED_UNICODE)]);
        }
        if (is_string($bonus) && trim($bonus) === '') {
            $this->merge(['bonus' => null]);
        }
    }
}
