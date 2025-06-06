<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Section;
use Illuminate\Validation\Rule;

/**
 * FormRequest pour la création d'une section dynamique.
 *
 * Valide les champs principaux d'une section et vérifie l'autorisation via la policy.
 */
class StoreSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Section::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page_id' => ['required', 'exists:pages,id'],
            'order' => ['required', 'integer'],
            'type' => ['required', 'string', 'max:100'],
            'params' => ['sometimes', 'array'],
            'is_visible' => ['sometimes', 'boolean'],
            'state' => ['sometimes', 'string', Rule::in(Section::STATES)],
        ];
    }

    protected function prepareForValidation()
    {
        $data = $this->all();
        if (isset($data['is_visible'])) {
            $this->merge([
                'is_visible' => filter_var($data['is_visible'], FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        if (!isset($data['params'])) {
            $this->merge([
                'params' => [],
            ]);
        }
        if (!isset($data['state'])) {
            $this->merge([
                'state' => Section::STATES['brouillon'],
            ]);
        }
    }
}
