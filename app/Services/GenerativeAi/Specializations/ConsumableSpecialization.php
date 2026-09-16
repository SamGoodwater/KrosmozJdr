<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\Specializations;

use App\Enums\EntityState;
use App\Models\Entity\Consumable;
use App\Services\GenerativeAi\AllowlistWriter;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\CreationGuideCatalog;
use App\Services\GenerativeAi\EntityGenerationProfile;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Support\Entity\EntityStateGate;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Spécialisation consommable : même noyau que l’objet, guide Création dédié.
 */
final class ConsumableSpecialization implements Specialization
{
    public function key(): string
    {
        return 'consumable';
    }

    public function entityType(): string
    {
        return 'consumable';
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
            return ['Aucun champ writable pour les consommables figés.'];
        }
        $errors = [];
        foreach ($fields as $field) {
            if (! array_key_exists($field, $payload) || ! is_string($payload[$field]) || trim($payload[$field]) === '') {
                $errors[] = "Champ « {$field} » requis.";
            }
        }

        return $errors;
    }

    public function persist(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        if ($request->entityId === null) {
            throw new RuntimeException('Consommable source requis.');
        }
        $row = Consumable::query()->findOrFail($request->entityId);
        EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);
        app(AllowlistWriter::class)->apply($row, $this->effectiveWritable($profile), $payload);

        return ['entity_id' => (int) $row->id, 'related_ids' => []];
    }

    public function compactExample(Model $model): array
    {
        return [
            'official_id' => $model->getAttribute('official_id'),
            'name' => $model->getAttribute('name'),
            'effect' => $model->getAttribute('effect'),
            'level' => $model->getAttribute('level'),
        ];
    }

    public function taskPrompt(): string
    {
        $fromConfig = GenerationConfigLoader::default()->forEntity('consumable')->extra['task_prompt'] ?? null;
        if (is_string($fromConfig) && trim($fromConfig) !== '') {
            return trim($fromConfig);
        }

        return (string) (app(CreationGuideCatalog::class)->promptFor('consumable')
            ?? 'Réécris uniquement les clés writable du consommable. Soins et utilitaires restent lisibles à table.');
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
            return ['name', 'description', 'effect'];
        }

        return [];
    }
}
