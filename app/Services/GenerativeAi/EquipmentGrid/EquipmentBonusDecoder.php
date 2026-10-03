<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

use App\Support\Entity\KrosmozItemBonusDecoder;

/**
 * Extrait un dictionnaire de bonus JDR (clés courtes) depuis `effect` / `bonus`.
 *
 * @see KrosmozItemBonusDecoder
 *
 * @example
 * $decoder->decode('{"strength":2}', null); // ['strength' => 2.0]
 */
final class EquipmentBonusDecoder
{
    public function __construct(
        private readonly KrosmozItemBonusDecoder $decoder = new KrosmozItemBonusDecoder,
    ) {}

    /**
     * @return array<string, float>
     */
    public function decode(mixed $effect, mixed $bonus = null): array
    {
        return $this->decoder->decode($effect, $bonus);
    }

    /**
     * @return array<string, float>
     */
    public function decodePayload(mixed $value): array
    {
        return $this->decoder->decodePayload($value);
    }
}
