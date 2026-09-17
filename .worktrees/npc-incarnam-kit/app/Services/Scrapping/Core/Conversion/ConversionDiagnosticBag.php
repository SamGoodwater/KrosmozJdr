<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion;

/**
 * Collecte les diagnostics non bloquants produits pendant une conversion.
 *
 * @example
 * $bag->manualReview('unknown_characteristic', 'Caractéristique DofusDB non mappée.', ['id' => 42]);
 */
final class ConversionDiagnosticBag
{
    /** @var list<array{level:string,code:string,message:string,context:array<string,mixed>}> */
    private array $entries = [];

    /**
     * @param  array<string, mixed>  $context
     */
    public function add(string $level, string $code, string $message, array $context = []): void
    {
        $this->entries[] = [
            'level' => $level,
            'code' => $code,
            'message' => $message,
            'context' => $context,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function manualReview(string $code, string $message, array $context = []): void
    {
        $this->add('manual_review', $code, $message, $context);
    }

    /** @return list<array{level:string,code:string,message:string,context:array<string,mixed>}> */
    public function all(): array
    {
        return $this->entries;
    }

    public function hasEntries(): bool
    {
        return $this->entries !== [];
    }

    public function requiresManualReview(): bool
    {
        foreach ($this->entries as $entry) {
            if ($entry['level'] === 'manual_review') {
                return true;
            }
        }

        return false;
    }
}
