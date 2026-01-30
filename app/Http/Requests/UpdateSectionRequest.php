<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Section;
use Illuminate\Validation\Rule;
use App\Enums\SectionType;

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
        $template = $this->input('template') ?? $this->input('type') ?? $this->route('section')?->template;
        
        // Convertir le template en string si c'est un enum
        // Si c'est déjà un SectionType, extraire sa valeur
        if ($template instanceof SectionType) {
            $template = $template->value;
        }
        
        $rules = [
            'page_id' => ['sometimes', 'exists:pages,id'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'template' => ['sometimes', Rule::enum(SectionType::class)],
            'type' => ['sometimes', Rule::enum(SectionType::class)],
            'settings' => ['sometimes', 'nullable', 'array'],
            'data' => ['sometimes', 'array'],
            'params' => ['sometimes', 'array'],
            'state' => ['sometimes', 'string', Rule::in([Section::STATE_RAW, Section::STATE_DRAFT, Section::STATE_PLAYABLE, Section::STATE_ARCHIVED])],
            'read_level' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'integer', 'min:0', 'max:5', 'gte:read_level'],
        ];

        // Validation dynamique des settings et data selon le template
        // $template est maintenant garanti d'être une string ou null
        if ($template && $sectionType = SectionType::tryFrom($template)) {
            $validationRules = $this->getTemplateValidationRules($sectionType);
            if (!empty($validationRules)) {
                // Rendre les règles conditionnelles pour l'update
                foreach ($validationRules as $key => $rule) {
                    if (str_starts_with($key, 'data.')) {
                        $rules[$key] = array_merge(['sometimes'], $rule);
                    } else {
                        $rules[$key] = $rule;
                    }
                }
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
                'data.content' => ['required_without:params.content', 'string'],
                'params.content' => ['required_without:data.content', 'string'],
                'settings.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'settings.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl'])],
            ],
            SectionType::IMAGE => [
                'data.src' => ['required_without:params.src', 'string'],
                'data.alt' => ['required_without:params.alt', 'string'],
                'params.src' => ['required_without:data.src', 'string'],
                'params.alt' => ['required_without:data.alt', 'string'],
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
                'data.src' => ['required_without:params.src', 'string'],
                'data.type' => ['required_without:params.type', 'string', Rule::in(['youtube', 'vimeo', 'direct'])],
                'params.src' => ['required_without:data.src', 'string'],
                'params.type' => ['required_without:data.type', 'string', Rule::in(['youtube', 'vimeo', 'direct'])],
                'settings.autoplay' => ['sometimes', 'boolean'],
                'settings.controls' => ['sometimes', 'boolean'],
            ],
            SectionType::ENTITY_TABLE => [
                'data.entity' => ['required_without:params.entity', 'string'],
                'params.entity' => ['required_without:data.entity', 'string'],
                'data.filters' => ['sometimes', 'array'],
                'data.columns' => ['sometimes', 'array'],
            ],
        };
    }

    /**
     * @description
     * Normalise les payloads legacy {type, params} vers le format moderne {template, data}.
     */
    protected function passedValidation(): void
    {
        $type = $this->input('type');
        $template = $this->input('template');
        $params = $this->input('params');
        $data = $this->input('data');

        $this->merge([
            'template' => $template ?? $type,
            'type' => $type ?? $template,
            'data' => $data ?? $params,
            'params' => $params ?? $data,
        ]);
    }
}
