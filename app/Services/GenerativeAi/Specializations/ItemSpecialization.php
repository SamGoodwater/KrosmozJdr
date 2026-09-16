<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\Specializations;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Services\GenerativeAi\AllowlistWriter;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\CreationGuideCatalog;
use App\Services\GenerativeAi\EntityGenerationProfile;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Support\Entity\EntityStateGate;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Spécialisation équipement : delta writable (uniques / retravail, pas un dump Dofus).
 */
final class ItemSpecialization implements Specialization
{
    public function key(): string
    {
        return 'item';
    }

    public function entityType(): string
    {
        return 'item';
    }

    public function jsonSchema(EntityGenerationProfile $profile): array
    {
        $fields = $this->effectiveWritable($profile);
        $properties = [];
        foreach ($fields as $field) {
            $properties[$field] = ['type' => 'string'];
        }

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => $fields,
            'properties' => $properties === [] ? new \stdClass : $properties,
        ];
    }

    public function validate(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        $fields = $this->effectiveWritable($profile);
        if ($fields === []) {
            return ['Aucun champ writable pour les objets : l’IA ne retravaille pas une fiche figée.'];
        }
        $errors = [];
        foreach ($fields as $field) {
            if (! array_key_exists($field, $payload) || ! is_string($payload[$field]) || trim($payload[$field]) === '') {
                $errors[] = "Champ « {$field} » requis.";
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
            throw new RuntimeException('Objet source requis.');
        }
        $item = Item::query()->findOrFail($request->entityId);
        EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);
        app(AllowlistWriter::class)->apply($item, $this->effectiveWritable($profile), $payload);

        return ['entity_id' => (int) $item->id, 'related_ids' => []];
    }

    public function compactExample(Model $model): array
    {
        return [
            'official_id' => $model->getAttribute('official_id'),
            'name' => $model->getAttribute('name'),
            'level' => $model->getAttribute('level'),
            'bonus' => $model->getAttribute('bonus'),
            'effect' => $model->getAttribute('effect'),
        ];
    }

    public function taskPrompt(): string
    {
        $fromConfig = GenerationConfigLoader::default()->forEntity('item')->extra['task_prompt'] ?? null;
        if (is_string($fromConfig) && trim($fromConfig) !== '') {
            return trim($fromConfig);
        }

        return (string) (app(CreationGuideCatalog::class)->promptFor('item')
            ?? 'Ne réécris que les clés writable. Identité Dofus figée. Bonus JDR dans bonus, auto_update false.');
    }

    /**
     * @return list<string>
     */
    private function effectiveWritable(EntityGenerationProfile $profile): array
    {
        if ($profile->writableFields !== []) {
            return $profile->writableFields;
        }
        if (! $profile->hasDofusSource) {
            return ['name', 'description', 'bonus', 'effect'];
        }

        return [];
    }
}
