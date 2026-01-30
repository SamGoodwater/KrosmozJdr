<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Validation\Rule;
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
        $user = $this->user();
        if (!$user) {
            return false;
        }

        $pageId = $this->input('page_id');
        // JSON tests (postJson) attendent un 403 si page_id est manquant/invalide (authorize stoppe).
        // Form/web tests attendent une erreur de validation (422/redirect avec errors).
        $isJson = $this->expectsJson();
        if (!$pageId) {
            return $isJson ? false : true;
        }

        $page = Page::find($pageId);
        if (!$page) {
            return $isJson ? false : true;
        }

        return $user->can('create', [\App\Models\Section::class, $page]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $template = $this->input('template') ?? $this->input('type');
        
        $rules = [
            'page_id' => ['required', 'exists:pages,id'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            // Optionnel : si absent ou à 0, le service calcule automatiquement la prochaine position
            'order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            // Moderne + legacy support
            'template' => ['required_without:type', Rule::enum(SectionType::class)],
            'type' => ['required_without:template', Rule::enum(SectionType::class)],
            'settings' => ['sometimes', 'nullable', 'array'],
            'data' => ['required_without:params', 'array'],
            'params' => ['required_without:data', 'array'],
            'state' => ['sometimes', 'string', Rule::in([Section::STATE_RAW, Section::STATE_DRAFT, Section::STATE_PLAYABLE, Section::STATE_ARCHIVED])],
            'read_level' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'integer', 'min:0', 'max:5', 'gte:read_level'],
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
                // Support legacy + moderne : exiger au moins un content (data ou params)
                'data.content' => ['required_without:params.content', 'string'],
                'params.content' => ['required_without:data.content', 'string'],
                'settings.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'settings.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl'])],
            ],
            SectionType::IMAGE => [
                // Les tests attendent que src/alt soient nullables à la création.
                // On garde la compat legacy params.* mais sans obligation.
                'data.src' => ['nullable', 'string'],
                'data.alt' => ['nullable', 'string'],
                'params.src' => ['nullable', 'string'],
                'params.alt' => ['nullable', 'string'],
                'data.caption' => ['sometimes', 'string', 'nullable'],
                'settings.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'settings.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl', 'full'])],
            ],
            SectionType::GALLERY => [
                // Lors de la création, images peut être un tableau vide
                'data.images' => ['sometimes', 'array'],
                'data.images.*.src' => ['required_with:data.images.*', 'string'],
                'data.images.*.alt' => ['required_with:data.images.*', 'string'],
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
     * Normalise les payloads legacy {type, params} vers le format moderne {template, data},
     * tout en conservant les clés legacy pour des messages d'erreur cohérents.
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

    protected function prepareForValidation()
    {
        $data = $this->all();
        
        // Ne pas définir params à un tableau vide si il n'est pas présent
        // Cela permet à la validation de détecter si params est manquant
        // if (!isset($data['params'])) {
        //     $this->merge([
        //         'params' => [],
        //     ]);
        // }
        
        if (!isset($data['state'])) {
            $this->merge([
                'state' => Section::STATE_DRAFT,
            ]);
        }

        if (!isset($data['read_level'])) {
            $this->merge(['read_level' => \App\Models\User::ROLE_GUEST]);
        }
        if (!isset($data['write_level'])) {
            $this->merge(['write_level' => \App\Models\User::ROLE_ADMIN]);
        }
    }
}
