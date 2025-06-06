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
        return $this->user() && in_array($this->user()->role, ['admin', 'super_admin']);
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
            'state' => ['sometimes', 'required', 'integer', 'in:0,1,2,3'],
            'usable' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:1'],
            'is_visible' => ['sometimes', 'nullable', 'string', 'in:guest,user,player,game_master,admin,super_admin'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
