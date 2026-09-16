<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\Specializations;

use App\Enums\EntityState;
use App\Models\Entity\Spell;
use App\Services\GenerativeAi\AllowlistWriter;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\CreationGuideCatalog;
use App\Services\GenerativeAi\EntityGenerationProfile;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Support\Entity\EntityStateGate;
use Illuminate\Database\Eloquent\Model;

/**
 * Spécialisation sort : delta `effect` (1 principal + 0–2 secondaires).
 */
final class SpellSpecialization implements Specialization
{
    public function key(): string
    {
        return 'spell';
    }

    public function entityType(): string
    {
        return 'spell';
    }

    public function jsonSchema(EntityGenerationProfile $profile): array
    {
        $fields = $profile->writableFields !== [] ? $profile->writableFields : ['effect'];
        $properties = [];
        foreach ($fields as $field) {
            $properties[$field] = ['type' => 'string'];
        }

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => array_values($fields),
            'properties' => $properties,
        ];
    }

    public function validate(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        $errors = [];
        $fields = $profile->writableFields !== [] ? $profile->writableFields : ['effect'];
        foreach ($fields as $field) {
            $value = $payload[$field] ?? null;
            if (! is_string($value) || trim($value) === '') {
                $errors[] = "Champ « {$field} » requis.";
            }
        }

        $effect = is_string($payload['effect'] ?? null) ? trim((string) $payload['effect']) : '';
        if ($effect !== '') {
            $parts = preg_split('/[\n;•]+/u', $effect) ?: [];
            $parts = array_values(array_filter(array_map('trim', $parts)));
            $max = (int) (GenerationConfigLoader::default()->get('generation.max_effects_per_spell', 3) ?: 3);
            if (count($parts) > $max) {
                $errors[] = "Trop d’effets ({$max} max : 1 principal + secondaires).";
            }
        }

        foreach (array_keys($payload) as $key) {
            if (! in_array($key, $fields, true)) {
                $errors[] = "Clé non autorisée : {$key}.";
            }
        }

        return $errors;
    }

    public function persist(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        if ($request->entityId === null) {
            throw new \RuntimeException('Sort source requis.');
        }
        $spell = Spell::query()->findOrFail($request->entityId);
        $writable = $profile->writableFields !== [] ? $profile->writableFields : ['effect'];
        EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);
        app(AllowlistWriter::class)->apply($spell, $writable, $payload);

        return ['entity_id' => (int) $spell->id, 'related_ids' => []];
    }

    public function compactExample(Model $model): array
    {
        return [
            'official_id' => $model->getAttribute('official_id'),
            'name' => $model->getAttribute('name'),
            'effect' => $model->getAttribute('effect'),
            'pa' => $model->getAttribute('pa'),
            'element' => $model->getAttribute('element'),
        ];
    }

    public function taskPrompt(): string
    {
        $fromConfig = GenerationConfigLoader::default()->forEntity('spell')->extra['task_prompt'] ?? null;
        if (is_string($fromConfig) && trim($fromConfig) !== '') {
            return trim($fromConfig);
        }

        return (string) (app(CreationGuideCatalog::class)->promptFor('spell')
            ?? 'Réécris le champ effect : 1 effet principal + 0 à 2 secondaires jouables à table. Garde nom, classe, élément.');
    }
}
