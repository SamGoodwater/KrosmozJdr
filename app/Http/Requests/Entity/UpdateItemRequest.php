<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
            'level' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'effect' => ['nullable', 'string'],
            'bonus' => ['nullable', 'string'],
            'recipe' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'rarity' => ['nullable', 'integer'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'usable' => ['nullable', 'integer'],
            'is_visible' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'item_type_id' => ['nullable', 'integer', 'exists:type_item_types,id'],
        ];
    }
}
