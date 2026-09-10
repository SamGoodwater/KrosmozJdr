<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Models\IaGenerationSetting;

/**
 * Persistance des réglages IA : une ligne en base surcharge le JSON du dépôt.
 *
 * @example
 * $payload = $store->currentPayload();
 */
final class GenerationConfigStore
{
    public function __construct(
        private readonly string $configPath,
    ) {}

    public static function default(): self
    {
        return new self(base_path('resources/ia/generation.json'));
    }

    /**
     * Payload en base, ou null pour retomber sur le fichier.
     *
     * @return array<string, mixed>|null
     */
    public function storedPayload(): ?array
    {
        $row = IaGenerationSetting::query()->orderBy('id')->first();
        if ($row === null || ! is_array($row->payload) || $row->payload === []) {
            return null;
        }

        return $row->payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function filePayload(): array
    {
        return (new GenerationConfigLoader($this->configPath))->raw();
    }

    /**
     * Payload effectif (base si présente, sinon JSON).
     *
     * @return array<string, mixed>
     */
    public function currentPayload(): array
    {
        return $this->storedPayload() ?? $this->filePayload();
    }

    public function isStored(): bool
    {
        return $this->storedPayload() !== null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function save(array $payload, ?int $userId): IaGenerationSetting
    {
        GenerationConfigLoader::assertValid($payload);

        $row = IaGenerationSetting::query()->orderBy('id')->first() ?? new IaGenerationSetting;
        $row->payload = $payload;
        $row->updated_by = $userId;
        $row->save();

        return $row;
    }

    public function reset(): void
    {
        IaGenerationSetting::query()->delete();
    }
}
