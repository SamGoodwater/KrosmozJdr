<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Enums\EntityState;
use App\Support\Entity\EntityStateGate;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Writer allowlist : jamais d’unguard du JSON LLM. État `auto` uniquement.
 *
 * @example app(AllowlistWriter::class)->apply($spell, ['effect'], ['effect' => '1d6']);
 */
final class AllowlistWriter
{
    /**
     * @param  list<string>  $allowed
     * @param  array<string, mixed>  $payload
     */
    public function apply(Model $model, array $allowed, array $payload, bool $setAutoState = true): Model
    {
        $filtered = [];
        foreach ($allowed as $field) {
            if (! array_key_exists($field, $payload)) {
                continue;
            }
            $filtered[$field] = $payload[$field];
        }

        if (array_key_exists('state', $filtered) || array_key_exists('auto_update', $filtered)) {
            throw new InvalidArgumentException('Le JSON LLM ne peut pas poser state ni auto_update.');
        }

        if ($setAutoState) {
            EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);
            $model->setAttribute('state', EntityState::Auto->value);
        }

        if ($this->modelHasColumn($model, 'auto_update')) {
            $model->setAttribute('auto_update', false);
        }

        foreach ($filtered as $field => $value) {
            if ($this->isGuardedMeta($field)) {
                continue;
            }
            $model->setAttribute($field, $value);
        }

        $model->save();

        return $model;
    }

    /**
     * @param  list<string>  $writable
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function onlyWritable(array $writable, array $payload): array
    {
        $out = [];
        foreach ($writable as $field) {
            if (array_key_exists($field, $payload) && ! $this->isGuardedMeta($field)) {
                $out[$field] = $payload[$field];
            }
        }

        return $out;
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

    private function modelHasColumn(Model $model, string $column): bool
    {
        return $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column);
    }
}
