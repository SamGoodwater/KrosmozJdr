<?php

declare(strict_types=1);

namespace App\Services\Entity;

use App\Enums\EntityState;
use App\Services\GenerativeAi\AllowlistWriter;
use App\Support\Entity\EntityStateGate;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use RuntimeException;

/**
 * Injection JSON manuelle sur n’importe quelle entité du registre (hors paquet IA spécialisé).
 *
 * Allowlist = fillable moins méta (state, id, niveaux…). Pose l’état `auto`.
 *
 * @example app(GenericEntityJsonInjector::class)->persist($breed, ['name' => 'Iop']);
 */
final class GenericEntityJsonInjector
{
    /**
     * Champs acceptés dans le JSON (fillable hors méta protégée).
     *
     * @return list<string>
     */
    public function writableFields(Model $model): array
    {
        $fillable = $model->getFillable();
        if ($fillable === []) {
            throw new RuntimeException('Aucun champ fillable pour cette entité : injection JSON impossible.');
        }

        $out = [];
        foreach ($fillable as $field) {
            if (! is_string($field) || $field === '' || $this->isGuardedMeta($field)) {
                continue;
            }
            $out[] = $field;
        }

        if ($out === []) {
            throw new RuntimeException('Aucun champ injectable pour cette entité.');
        }

        return array_values($out);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return list<string>
     */
    public function validate(Model $model, array $payload): array
    {
        if ($payload === []) {
            return ['Le JSON doit contenir au moins un champ.'];
        }

        $allowed = $this->writableFields($model);
        $errors = [];
        foreach (array_keys($payload) as $key) {
            if (! is_string($key) || $key === '') {
                $errors[] = 'Clé JSON invalide.';

                continue;
            }
            if (! in_array($key, $allowed, true)) {
                $errors[] = "Clé non autorisée : {$key}.";
            }
        }

        return $errors;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{entity_id: int, related_ids: list<int>}
     */
    public function persist(Model $model, array $payload): array
    {
        $errors = $this->validate($model, $payload);
        if ($errors !== []) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }

        $allowed = $this->writableFields($model);
        EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);
        app(AllowlistWriter::class)->apply($model, $allowed, $payload);

        return [
            'entity_id' => (int) $model->getKey(),
            'related_ids' => [],
        ];
    }

    /**
     * Schéma / exemple pour le bouton « Exemple » (champs scalaires).
     *
     * @return array{schema: array<string, mixed>, example: array<string, mixed>}
     */
    public function schemaAndExample(Model $model): array
    {
        $fields = $this->writableFields($model);
        $properties = [];
        $example = [];
        foreach ($fields as $field) {
            $properties[$field] = ['type' => 'string'];
            $example[$field] = '';
        }

        return [
            'schema' => [
                'type' => 'object',
                'additionalProperties' => false,
                'properties' => $properties === [] ? new \stdClass : $properties,
            ],
            'example' => $example,
        ];
    }

    private function isGuardedMeta(string $field): bool
    {
        return in_array($field, [
            'id',
            'state',
            'auto_update',
            'created_by',
            'created_at',
            'updated_at',
            'deleted_at',
            'read_level',
            'write_level',
        ], true);
    }
}
