<?php

namespace App\Http\Requests;

use App\Enums\EntityState;
use App\Models\Page;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $pageId = $this->route('page')?->id;

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:pages,slug,'.$pageId, 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'in_menu' => ['sometimes', 'boolean'],
            'state' => ['sometimes', 'string', EntityState::rule()],
            'read_level' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'parent_id' => ['nullable', 'exists:pages,id'],
            'menu_order' => ['sometimes', 'integer'],
            'menu_group' => ['nullable', 'string', 'max:100'],
            'entity_key' => ['nullable', 'string', 'max:50', Rule::in(config('entities.keys', []))],
            'icon' => ['nullable', 'string', 'max:255'],
            'page_css_classes' => ['nullable', 'string', 'max:500'],
            'title_css_classes' => ['nullable', 'string', 'max:500'],
            'menu_item_css_classes' => ['nullable', 'string', 'max:500'],
            'settings' => ['nullable', 'array'],
            'settings.show_rules_breadcrumb' => ['sometimes', 'boolean'],
            'settings.menu_collapsible' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $page = $this->route('page');
            if (! $page instanceof Page) {
                return;
            }

            if (! $page->isCriticalPage()) {
                return;
            }

            if (! $this->has('slug')) {
                return;
            }

            $requestedSlug = (string) $this->input('slug', '');
            if ($requestedSlug !== (string) $page->slug) {
                $validator->errors()->add('slug', 'Le slug des pages critiques ne peut pas être modifié.');
            }
        });
    }

    protected function prepareForValidation()
    {
        $data = $this->all();
        if (isset($data['title']) && ! isset($data['slug'])) {
            $this->merge([
                'slug' => Str::slug($data['title']),
            ]);
        }
        if (isset($data['in_menu'])) {
            $this->merge([
                'in_menu' => filter_var($data['in_menu'], FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        if (array_key_exists('menu_group', $data)) {
            $group = trim((string) $data['menu_group']);
            $this->merge(['menu_group' => $group === '' ? null : $group]);
        }
        if (array_key_exists('entity_key', $data)) {
            $key = trim((string) $data['entity_key']);
            $this->merge(['entity_key' => $key === '' ? null : $key]);
        }
        foreach (['page_css_classes', 'title_css_classes', 'menu_item_css_classes'] as $field) {
            if (array_key_exists($field, $data)) {
                $val = trim((string) $data[$field]);
                $this->merge([$field => $val === '' ? null : $val]);
            }
        }

        if (array_key_exists('settings', $data)) {
            $settings = $data['settings'];
            if (! is_array($settings)) {
                $settings = [];
            }
            if (array_key_exists('show_rules_breadcrumb', $settings)) {
                $settings['show_rules_breadcrumb'] = filter_var($settings['show_rules_breadcrumb'], FILTER_VALIDATE_BOOLEAN);
            }
            if (array_key_exists('menu_collapsible', $settings)) {
                $settings['menu_collapsible'] = filter_var($settings['menu_collapsible'], FILTER_VALIDATE_BOOLEAN);
            }
            $this->merge(['settings' => $settings]);
        }
    }
}
