<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Page;
use Illuminate\Validation\Rule;

/**
 * FormRequest pour la mise à jour d'une page dynamique.
 *
 * Valide les champs modifiables d'une page et vérifie l'autorisation via la policy.
 */
class UpdatePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $page = $this->route('page');
        return $this->user()?->can('update', $page) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $pageId = $this->route('page')?->id;
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:pages,slug,' . $pageId, 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'is_visible' => ['sometimes', 'boolean'],
            'in_menu' => ['sometimes', 'boolean'],
            'state' => ['sometimes', 'string', Rule::in(Page::STATES)],
            'parent_id' => ['nullable', 'exists:pages,id'],
            'menu_order' => ['sometimes', 'integer'],
        ];
    }

    protected function prepareForValidation()
    {
        $data = $this->all();
        if (isset($data['title']) && !isset($data['slug'])) {
            $this->merge([
                'slug' => \Str::slug($data['title']),
            ]);
        }
        if (isset($data['is_visible'])) {
            $this->merge([
                'is_visible' => filter_var($data['is_visible'], FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        if (isset($data['in_menu'])) {
            $this->merge([
                'in_menu' => filter_var($data['in_menu'], FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
