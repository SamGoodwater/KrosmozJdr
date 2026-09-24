<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use InvalidArgumentException;
use JsonException;

/**
 * Parse et nettoie un paquet JSON manuel (comme si c’était le LLM).
 *
 * @example $payload = app(ManualJsonPayloadSanitizer::class)->parseAndSanitize($raw);
 */
final class ManualJsonPayloadSanitizer
{
    public const MAX_BYTES = 262_144;

    public const MAX_DEPTH = 12;

    /**
     * @return array<string, mixed>
     */
    public function parseAndSanitize(mixed $raw): array
    {
        $payload = $this->decode($raw);
        $this->assertDepth($payload, 0);

        return $this->sanitizeNode($payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(mixed $raw): array
    {
        if (is_array($raw)) {
            if ($raw === []) {
                throw new InvalidArgumentException('Le JSON doit être un objet non vide.');
            }
            if (array_is_list($raw)) {
                throw new InvalidArgumentException('Le JSON racine doit être un objet, pas un tableau.');
            }

            return $raw;
        }

        if (! is_string($raw)) {
            throw new InvalidArgumentException('Payload JSON invalide.');
        }

        $trimmed = trim($raw);
        if ($trimmed === '') {
            throw new InvalidArgumentException('Le JSON est vide.');
        }
        if (strlen($trimmed) > self::MAX_BYTES) {
            throw new InvalidArgumentException('JSON trop volumineux (max 256 Ko).');
        }

        try {
            $decoded = json_decode($trimmed, true, self::MAX_DEPTH + 2, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('JSON invalide : '.$exception->getMessage(), 0, $exception);
        }

        if (! is_array($decoded) || $decoded === [] || array_is_list($decoded)) {
            throw new InvalidArgumentException('Le JSON racine doit être un objet non vide.');
        }

        return $decoded;
    }

    private function assertDepth(mixed $node, int $depth): void
    {
        if ($depth > self::MAX_DEPTH) {
            throw new InvalidArgumentException('JSON trop profond (max '.self::MAX_DEPTH.' niveaux).');
        }
        if (! is_array($node)) {
            return;
        }
        foreach ($node as $child) {
            $this->assertDepth($child, $depth + 1);
        }
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function sanitizeNode(array $node): array
    {
        $out = [];
        foreach ($node as $key => $value) {
            if (! is_string($key) || $key === '') {
                continue;
            }
            $out[$key] = $this->sanitizeValue($value);
        }

        return $out;
    }

    private function sanitizeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            return strip_tags($value);
        }
        if (is_int($value) || is_float($value) || is_bool($value) || $value === null) {
            return $value;
        }
        if (is_array($value)) {
            if (array_is_list($value)) {
                return array_map(fn (mixed $item): mixed => $this->sanitizeValue($item), $value);
            }

            return $this->sanitizeNode($value);
        }

        throw new InvalidArgumentException('Type JSON non supporté dans le paquet.');
    }
}
