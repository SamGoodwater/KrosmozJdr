<?php

declare(strict_types=1);

namespace App\Support\Entity;

use App\Services\Characteristic\Compatibility\CharacteristicCompatibilityService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;

/**
 * Valide un JSON `effect` jouable (objet plat clé → entier) pour les items.
 *
 * @example
 *   $errors = $validator->validateFlatEffectJson('{"strength":2}', 5);
 */
final class KrosmozItemEffectValidator
{
    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly CharacteristicLimitService $limitService,
        private readonly CharacteristicCompatibilityService $compatibility,
    ) {}

    /**
     * @return list<string> Messages d'erreur (vide si valide ou effect vide).
     */
    public function validateFlatEffectJson(?string $effect, ?int $itemTypeId): array
    {
        if ($effect === null || trim($effect) === '') {
            return [];
        }

        $trimmed = trim($effect);
        if ($trimmed !== '' && $trimmed[0] === '<') {
            return ['effect : format texte non validé ici (utiliser un objet JSON plat).'];
        }

        try {
            $decoded = json_decode($trimmed, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return ['effect : JSON invalide.'];
        }

        if (! is_array($decoded)) {
            return ['effect : doit être un objet JSON.'];
        }

        if ($decoded === []) {
            return [];
        }

        if (! $this->isFlatPlayableObject($decoded)) {
            return ['effect : doit être un objet plat clé → entier (pas un tableau d’effets DofusDB).'];
        }

        return $this->validateBonusMap($this->flatIntegerMap($decoded), $itemTypeId);
    }

    /**
     * @param  array<string, int>  $bonuses
     * @return list<string>
     */
    public function validateBonusMap(array $bonuses, ?int $itemTypeId): array
    {
        $errors = [];
        foreach ($bonuses as $shortKey => $value) {
            if (! is_string($shortKey) || $shortKey === '') {
                $errors[] = 'effect : clé invalide.';

                continue;
            }

            $objectKey = str_ends_with($shortKey, '_object') ? $shortKey : "{$shortKey}_object";
            if ($this->getter->getDefinition($objectKey, 'item') === null) {
                $errors[] = "{$shortKey} : caractéristique objet inconnue.";

                continue;
            }

            if (! $this->compatibility->isObjectBonusAllowed($shortKey, $itemTypeId)) {
                $errors[] = "{$shortKey} : incompatible avec le type d’équipement.";

                continue;
            }

            $result = $this->limitService->validateSingle($objectKey, $value, 'item');
            if (! $result->isValid()) {
                foreach ($result->getErrors() as $err) {
                    $errors[] = (string) ($err['message'] ?? "{$shortKey} hors limites.");
                }
            }
        }

        return $errors;
    }

    /**
     * @param  array<mixed, mixed>  $decoded
     */
    private function isFlatPlayableObject(array $decoded): bool
    {
        if (array_is_list($decoded)) {
            return false;
        }
        foreach ($decoded as $key => $raw) {
            if (! is_string($key) || $key === '' || is_numeric($key)) {
                return false;
            }
            if (is_array($raw)) {
                return false;
            }
            if (! is_int($raw) && ! (is_string($raw) && ctype_digit(trim($raw)))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<mixed, mixed>  $decoded
     * @return array<string, int>
     */
    private function flatIntegerMap(array $decoded): array
    {
        $out = [];
        foreach ($decoded as $key => $raw) {
            if (! is_string($key)) {
                continue;
            }
            $out[$key] = (int) $raw;
        }

        return $out;
    }
}
