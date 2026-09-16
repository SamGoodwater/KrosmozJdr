<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\Specializations;

use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\EntityGenerationProfile;
use Illuminate\Database\Eloquent\Model;

/**
 * Spécialisation d’un type : schéma, validateurs, persist. Le noyau reste générique.
 */
interface Specialization
{
    public function key(): string;

    public function entityType(): string;

    /**
     * @return array<string, mixed>
     */
    public function jsonSchema(EntityGenerationProfile $profile): array;

    /**
     * @param  array<string, mixed>  $payload
     * @return list<string>
     */
    public function validate(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array;

    /**
     * @param  array<string, mixed>  $payload
     * @return array{entity_id: int, related_ids: list<int>}
     */
    public function persist(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array;

    /**
     * @return array<string, mixed>
     */
    public function compactExample(Model $model): array;

    public function taskPrompt(): string;
}
