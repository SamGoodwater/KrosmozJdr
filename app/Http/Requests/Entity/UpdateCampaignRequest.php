<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'unique:campaigns,slug,' . $this->route('campaign')],
            'keyword' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_public' => ['sometimes', 'required', 'boolean'],
            'progress_state' => ['sometimes', 'required', 'integer', 'in:0,1,2,3'],
            'state' => ['sometimes', 'nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
