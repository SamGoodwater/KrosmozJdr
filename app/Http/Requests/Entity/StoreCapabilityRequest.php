<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la création d'une Capability.
 *
 * Valide les champs principaux d'une capacité.
 */
class StoreCapabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'effect' => ['nullable', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'pa' => ['nullable', 'string', 'max:255'],
            'po' => ['nullable', 'string', 'max:255'],
            'po_editable' => ['nullable', 'boolean'],
            'time_before_use_again' => ['nullable', 'string', 'max:255'],
            'casting_time' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'element' => ['nullable', 'string', 'max:255'],
            'is_magic' => ['nullable', 'boolean'],
            'ritual_available' => ['nullable', 'boolean'],
            'powerful' => ['nullable', 'string', 'max:255'],
            'usable' => ['nullable', 'integer', 'in:0,1'],
            'is_visible' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
