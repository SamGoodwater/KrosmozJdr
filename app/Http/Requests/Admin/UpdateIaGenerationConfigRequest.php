<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Entity\Panoply;
use App\Services\GenerativeAi\AnthropicModelCatalog;
use App\Services\GenerativeAi\FewShotExamplePool;
use App\Services\GenerativeAi\FewShotPanoplyGuard;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Services\GenerativeAi\GenerationConfigStore;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateIaGenerationConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $generation = $this->input('generation', []);
        if (is_array($generation)) {
            foreach (['max_retries', 'few_shot_count', 'max_effects_per_spell'] as $key) {
                if (array_key_exists($key, $generation) && is_numeric($generation[$key])) {
                    $generation[$key] = (int) $generation[$key];
                }
            }
            if (array_key_exists('prompt_cache', $generation)) {
                $generation['prompt_cache'] = filter_var(
                    $generation['prompt_cache'],
                    FILTER_VALIDATE_BOOLEAN
                );
            }
            $this->merge(['generation' => $generation]);
        }

        $entities = $this->input('entities', []);
        if (! is_array($entities)) {
            return;
        }
        foreach (GenerationConfigLoader::ENTITY_TYPES as $type) {
            if (! isset($entities[$type]) || ! is_array($entities[$type])) {
                continue;
            }
            $entities[$type]['has_dofus_source'] = filter_var(
                $entities[$type]['has_dofus_source'] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );
            $ids = $entities[$type]['example_ids'] ?? [];
            if (is_array($ids)) {
                $entities[$type]['example_ids'] = $this->normalizeExampleRefs($ids);
            }
            if (isset($entities[$type]['task_prompt']) && is_string($entities[$type]['task_prompt'])) {
                $entities[$type]['task_prompt'] = $entities[$type]['task_prompt'];
            }
            if ($type === 'item') {
                $panoplies = $entities[$type]['few_shot_panoplies'] ?? [];
                if (is_array($panoplies)) {
                    $names = [];
                    foreach ($panoplies as $name) {
                        if (is_string($name) && trim($name) !== '') {
                            $names[] = trim($name);
                        }
                    }
                    $entities[$type]['few_shot_panoplies'] = array_values(array_unique($names));
                }
            }
        }
        $this->merge(['entities' => $entities]);

        $supervisor = $this->input('supervisor_prompt');
        if (is_string($supervisor)) {
            $this->merge(['supervisor_prompt' => $supervisor]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $entityRules = [];
        foreach (GenerationConfigLoader::ENTITY_TYPES as $entity) {
            $prefix = "entities.{$entity}";
            $entityRules["{$prefix}.has_dofus_source"] = ['required', 'boolean'];
            $entityRules["{$prefix}.frozen_fields"] = $this->frozenRule();
            $entityRules["{$prefix}.writable_fields"] = $this->stringListRule();
            $entityRules["{$prefix}.frozen_characteristics"] = $this->frozenRule();
            $entityRules["{$prefix}.writable_characteristics"] = $this->stringListRule();
            $entityRules["{$prefix}.example_ids"] = [
                'present',
                'array',
                'max:40',
                function (string $attribute, mixed $value, \Closure $fail) use ($entity): void {
                    $this->assertPlayableExampleRefs($entity, $value, $fail);
                },
            ];
            $entityRules["{$prefix}.example_ids.*"] = ['distinct'];
            $entityRules["{$prefix}.task_prompt"] = ['nullable', 'string', 'max:20000'];
        }
        $entityRules['entities.item.few_shot_panoplies'] = ['present', 'array', 'max:80'];
        $entityRules['entities.item.few_shot_panoplies.*'] = ['string', 'max:191', 'distinct'];

        return [
            'supervisor_prompt' => ['nullable', 'string', 'max:20000'],
            'generation.max_retries' => ['required', 'integer', 'min:0', 'max:5'],
            'generation.few_shot_count' => ['required', 'integer', 'min:0', 'max:30'],
            'generation.max_effects_per_spell' => ['required', 'integer', 'min:1', 'max:10'],
            'generation.model' => ['required', 'string', Rule::in(AnthropicModelCatalog::ids())],
            'generation.prompt_cache' => ['required', 'boolean'],
            ...$entityRules,
        ];
    }

    /**
     * Payload prêt à persister (clés libres du JSON conservées).
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $base = app(GenerationConfigStore::class)->currentPayload();
        $validated = $this->validated();

        $generation = is_array($base['generation'] ?? null) ? $base['generation'] : [];
        $payload = [
            'version' => is_int($base['version'] ?? null) ? $base['version'] : 1,
            'supervisor_prompt' => is_string($validated['supervisor_prompt'] ?? null)
                ? $validated['supervisor_prompt']
                : (is_string($base['supervisor_prompt'] ?? null) ? $base['supervisor_prompt'] : ''),
            'generation' => array_merge($generation, $validated['generation']),
            'entities' => [],
        ];

        $known = array_flip(GenerationConfigLoader::ENTITY_KNOWN_KEYS);
        foreach (GenerationConfigLoader::ENTITY_TYPES as $entity) {
            $previous = is_array($base['entities'][$entity] ?? null) ? $base['entities'][$entity] : [];
            $extra = array_diff_key($previous, $known);
            $payload['entities'][$entity] = array_merge($extra, $validated['entities'][$entity]);
            $payload['entities'][$entity]['has_dofus_source'] = (bool) $payload['entities'][$entity]['has_dofus_source'];
            $payload['entities'][$entity]['example_ids'] = app(FewShotExamplePool::class)
                ->normalizePlayableRefs($entity, $payload['entities'][$entity]['example_ids'] ?? []);
        }

        $panoplies = $payload['entities']['item']['few_shot_panoplies'] ?? [];
        if (is_array($panoplies)) {
            $names = [];
            foreach ($panoplies as $name) {
                if (is_string($name) && trim($name) !== '') {
                    $names[] = trim($name);
                }
            }
            // Contrôle souple : si aucune panoplie n'est en base (CI vide), on ne bloque pas l'enregistrement.
            try {
                if ($names !== [] && Panoply::query()->exists()) {
                    app(FewShotPanoplyGuard::class)->assertPlayable($names);
                }
            } catch (\RuntimeException $exception) {
                throw ValidationException::withMessages([
                    'entities.item.few_shot_panoplies' => $exception->getMessage(),
                ]);
            }
        }

        foreach ($base as $key => $value) {
            if (! is_string($key) || str_starts_with($key, '_')) {
                continue;
            }
            if (in_array($key, ['version', 'generation', 'entities'], true)) {
                continue;
            }
            if (! array_key_exists($key, $payload)) {
                $payload[$key] = $value;
            }
        }

        return $payload;
    }

    /**
     * @return list<mixed>
     */
    private function frozenRule(): array
    {
        return [
            'present',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value === '*') {
                    return;
                }
                $this->assertStringList($attribute, $value, $fail);
            },
        ];
    }

    /**
     * @return list<mixed>
     */
    private function stringListRule(): array
    {
        return [
            'present',
            'array',
            'max:80',
            function (string $attribute, mixed $value, \Closure $fail): void {
                $this->assertStringList($attribute, $value, $fail);
            },
        ];
    }

    /**
     * @param  list<mixed>  $ids
     * @return list<int|string>
     */
    private function normalizeExampleRefs(array $ids): array
    {
        $out = [];
        foreach ($ids as $id) {
            if (is_int($id) && $id > 0) {
                $out[] = $id;

                continue;
            }
            if (! is_string($id)) {
                continue;
            }
            $token = trim($id);
            if ($token === '') {
                continue;
            }
            if (ctype_digit($token)) {
                $out[] = (int) $token;

                continue;
            }
            $out[] = $token;
        }

        return array_values($out);
    }

    private function assertPlayableExampleRefs(string $entity, mixed $value, \Closure $fail): void
    {
        if (! is_array($value)) {
            $fail("entities.{$entity}.example_ids doit être une liste.");

            return;
        }
        try {
            app(FewShotExamplePool::class)->assertAllPlayable($entity, $value);
        } catch (\RuntimeException $exception) {
            $fail($exception->getMessage());
        }
    }

    private function assertStringList(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! is_array($value)) {
            $fail("{$attribute} doit être \"*\" ou une liste.");

            return;
        }
        if (count($value) > 80) {
            $fail("{$attribute} : trop d’entrées (max 80).");

            return;
        }
        foreach ($value as $item) {
            if (! is_string($item) || $item === '' || strlen($item) > 128 || preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $item) !== 1) {
                $fail("{$attribute} contient une clé invalide.");

                return;
            }
        }
    }
}
