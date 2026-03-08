<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Validation\Rule;
use App\Enums\SectionType;
use App\Support\SectionTemplateValidationRules;
use App\Support\SectionTemplatePayloadValidator;
use Illuminate\Validation\Validator;

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
            // Payload moderne/legacy : optionnel au niveau racine.
            // Les règles template imposent ensuite les champs requis.
            'data' => ['sometimes', 'array'],
            'params' => ['sometimes', 'array'],
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
        return SectionTemplateValidationRules::forTemplate($template);
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $templateRaw = $this->input('template') ?? $this->input('type');
            if ($templateRaw instanceof SectionType) {
                $templateRaw = $templateRaw->value;
            }

            $template = is_string($templateRaw) ? SectionType::tryFrom($templateRaw) : null;
            if (!$template) {
                return;
            }

            if ($template === SectionType::IMAGE) {
                foreach (['data.src', 'params.src'] as $key) {
                    $value = $this->input($key);
                    if (!is_string($value) || trim($value) === '') {
                        continue;
                    }
                    $error = SectionTemplatePayloadValidator::validateImageSource($value);
                    if ($error) {
                        $validator->errors()->add($key, $error);
                    }
                }
            }

            if ($template === SectionType::VIDEO) {
                $dataType = $this->input('data.type') ?? $this->input('params.type') ?? '';

                $dataSrc = $this->input('data.src');
                if (is_string($dataSrc) && trim($dataSrc) !== '') {
                    $error = SectionTemplatePayloadValidator::validateVideoSource((string) ($this->input('data.type') ?? $dataType), $dataSrc);
                    if ($error) {
                        $validator->errors()->add('data.src', $error);
                    }
                }

                $paramsSrc = $this->input('params.src');
                if (is_string($paramsSrc) && trim($paramsSrc) !== '') {
                    $error = SectionTemplatePayloadValidator::validateVideoSource((string) ($this->input('params.type') ?? $dataType), $paramsSrc);
                    if ($error) {
                        $validator->errors()->add('params.src', $error);
                    }
                }
            }

            if ($template === SectionType::LEGAL_MARKDOWN) {
                foreach (['data.sourceUrl', 'params.sourceUrl'] as $key) {
                    $value = $this->input($key);
                    if (!is_string($value) || trim($value) === '') {
                        continue;
                    }
                    $error = SectionTemplatePayloadValidator::validateLegalMarkdownSourceUrl($value);
                    if ($error) {
                        $validator->errors()->add($key, $error);
                    }
                }
            }
        });
    }

    protected function prepareForValidation()
    {
        $data = $this->all();

        // Compat moderne/legacy AVANT validation:
        // - template <-> type
        // - data <-> params
        $template = $data['template'] ?? null;
        $type = $data['type'] ?? null;
        $payloadData = $data['data'] ?? null;
        $params = $data['params'] ?? null;

        $merged = [];
        if (!isset($data['template']) && isset($data['type'])) {
            $merged['template'] = $type;
        }
        if (!isset($data['type']) && isset($data['template'])) {
            $merged['type'] = $template;
        }
        if (!isset($data['data']) && isset($data['params'])) {
            $merged['data'] = $params;
        }
        if (!isset($data['params']) && isset($data['data'])) {
            $merged['params'] = $payloadData;
        }
        if (!empty($merged)) {
            $this->merge($merged);
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
