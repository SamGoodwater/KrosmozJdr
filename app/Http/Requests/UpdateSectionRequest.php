<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Section;
use Illuminate\Validation\Rule;

/**
 * FormRequest pour la mise à jour d'une section dynamique.
 *
 * Valide les champs modifiables d'une section et vérifie l'autorisation via la policy.
 */
class UpdateSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $section = $this->route('section');
        return $this->user()?->can('update', $section) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page_id' => ['sometimes', 'exists:pages,id'],
            'order' => ['sometimes', 'integer'],
            'type' => ['sometimes', 'string', 'max:100'],
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
    }
}
