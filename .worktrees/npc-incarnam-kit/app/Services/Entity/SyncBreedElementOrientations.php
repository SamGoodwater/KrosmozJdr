<?php

namespace App\Services\Entity;

use App\Models\Entity\Breed;
use App\Models\Entity\BreedElementOrientation;
use Illuminate\Support\Facades\DB;

/**
 * Synchronise les paires (voix élémentaire → orientation) pour une classe.
 */
class SyncBreedElementOrientations
{
    /**
     * @param  array<string, string|null>|list<array{element: string, orientation_key: string|null}>|null  $payload
     */
    public function sync(Breed $breed, ?array $payload): void
    {
        if ($payload === null) {
            return;
        }

        $allowedElements = BreedElementOrientation::ELEMENTS;
        $allowedKeys = config('breed_element_orientations.allowed_orientation_keys', []);

        $pairs = $this->normalizePayload($payload);

        DB::transaction(function () use ($breed, $pairs, $allowedElements, $allowedKeys): void {
            $breed->elementOrientations()->delete();

            foreach ($pairs as $element => $orientationKey) {
                if (! in_array($element, $allowedElements, true)) {
                    continue;
                }
                if ($orientationKey === null || $orientationKey === '') {
                    continue;
                }
                if (! in_array($orientationKey, $allowedKeys, true)) {
                    continue;
                }

                $breed->elementOrientations()->create([
                    'element' => $element,
                    'orientation_key' => $orientationKey,
                ]);
            }
        });
    }

    /**
     * @return array<string, string|null>
     */
    private function normalizePayload(array $payload): array
    {
        if ($payload === []) {
            return [];
        }

        $first = reset($payload);
        if (is_array($first) && isset($first['element'])) {
            $out = [];
            foreach ($payload as $row) {
                if (! is_array($row) || ! isset($row['element'])) {
                    continue;
                }
                $el = (string) $row['element'];
                $out[$el] = isset($row['orientation_key']) && $row['orientation_key'] !== ''
                    ? (string) $row['orientation_key']
                    : null;
            }

            return $out;
        }

        /** @var array<string, mixed> $payload */
        $out = [];
        foreach ($payload as $key => $value) {
            $k = (string) $key;
            if ($value === null || $value === '') {
                $out[$k] = null;
            } else {
                $out[$k] = (string) $value;
            }
        }

        return $out;
    }
}
