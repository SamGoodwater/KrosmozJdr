<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Section;
use Illuminate\Validation\Rule;
use App\Enums\PageState;
use App\Enums\Visibility;
use App\Enums\SectionType;

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
        $template = $this->input('template');
        
        $rules = [
            'page_id' => ['required', 'exists:pages,id'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'order' => ['required', 'integer', 'min:0'],
            'template' => ['required', Rule::enum(SectionType::class)],
            'settings' => ['sometimes', 'nullable', 'array'],
            'data' => ['required', 'array'],
            'is_visible' => ['sometimes', Rule::enum(Visibility::class)],
            'can_edit_role' => ['sometimes', Rule::enum(Visibility::class)],
            'state' => ['sometimes', Rule::enum(PageState::class)],
        ];

        // Validation dynamique des settings et data selon le template
        if ($template && $sectionType = SectionType::tryFrom($template)) {
            $validationRules = $this->getTemplateValidationRules($sectionType);
            if (!empty($validationRules)) {
                $rules = array_merge($rules, $validationRules);
            }
        }

        return $rules;
    }

    /**
     * Retourne les règles de validation pour les settings et data selon le template de section.
     * 
     * @param SectionType $template Template de section
     * @return array<string, mixed> Règles de validation
     */
    protected function getTemplateValidationRules(SectionType $template): array
    {
        return match($template) {
            SectionType::TEXT => [
                'data.content' => ['required', 'string'],
                'settings.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'settings.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl'])],
            ],
            SectionType::IMAGE => [
                'data.src' => ['required', 'string'],
                'data.alt' => ['required', 'string'],
                'data.caption' => ['sometimes', 'string', 'nullable'],
                'settings.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'settings.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl', 'full'])],
            ],
            SectionType::GALLERY => [
                'data.images' => ['required', 'array', 'min:1'],
                'data.images.*.src' => ['required', 'string'],
                'data.images.*.alt' => ['required', 'string'],
                'data.images.*.caption' => ['sometimes', 'string', 'nullable'],
                'settings.columns' => ['sometimes', 'integer', Rule::in([2, 3, 4])],
                'settings.gap' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg'])],
            ],
            SectionType::VIDEO => [
                'data.src' => ['required', 'string'],
                'data.type' => ['required', 'string', Rule::in(['youtube', 'vimeo', 'direct'])],
                'settings.autoplay' => ['sometimes', 'boolean'],
                'settings.controls' => ['sometimes', 'boolean'],
            ],
            SectionType::ENTITY_TABLE => [
                'data.entity' => ['required', 'string'],
                'data.filters' => ['sometimes', 'array'],
                'data.columns' => ['sometimes', 'array'],
            ],
        };
    }

    protected function prepareForValidation()
    {
        $data = $this->all();
        
        if (!isset($data['is_visible'])) {
            $this->merge([
                'is_visible' => Visibility::GUEST->value,
            ]);
        }
        
        if (!isset($data['can_edit_role'])) {
            $this->merge([
                'can_edit_role' => Visibility::ADMIN->value,
            ]);
        }
        
        // Ne pas définir params à un tableau vide si il n'est pas présent
        // Cela permet à la validation de détecter si params est manquant
        // if (!isset($data['params'])) {
        //     $this->merge([
        //         'params' => [],
        //     ]);
        // }
        
        if (!isset($data['state'])) {
            $this->merge([
                'state' => PageState::DRAFT->value,
            ]);
        }
    }
}
