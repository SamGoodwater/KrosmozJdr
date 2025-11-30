<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpellRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'effect' => ['nullable', 'string'],
            'area' => ['nullable', 'integer'],
            'level' => ['nullable', 'string', 'max:255'],
            'po' => ['nullable', 'string', 'max:255'],
            'po_editable' => ['nullable', 'boolean'],
            'pa' => ['nullable', 'string', 'max:255'],
            'cast_per_turn' => ['nullable', 'string', 'max:255'],
            'cast_per_target' => ['nullable', 'string', 'max:255'],
            'sight_line' => ['nullable', 'boolean'],
            'number_between_two_cast' => ['nullable', 'string', 'max:255'],
            'number_between_two_cast_editable' => ['nullable', 'boolean'],
            'element' => ['nullable', 'integer'],
            'category' => ['nullable', 'integer'],
            'is_magic' => ['nullable', 'boolean'],
            'powerful' => ['nullable', 'integer'],
            'usable' => ['nullable', 'integer'],
            'is_visible' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
        ];
    }
}
