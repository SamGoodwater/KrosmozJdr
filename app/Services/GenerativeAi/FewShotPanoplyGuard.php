<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Panoply;
use RuntimeException;

/**
 * Vérifie que les panoplies few-shot sont `playable` (noms, pas d’ids SQL).
 *
 * @example app(FewShotPanoplyGuard::class)->assertPlayable(['Panoplie du Bouftou']);
 */
final class FewShotPanoplyGuard
{
    /**
     * @param  list<string>  $names
     */
    public function assertPlayable(array $names): void
    {
        if ($names === []) {
            return;
        }

        $missing = [];
        $found = 0;
        foreach ($names as $name) {
            $token = trim($name);
            if ($token === '') {
                continue;
            }
            $exists = Panoply::query()
                ->where('state', EntityState::Playable->value)
                ->where('name', $token)
                ->exists();
            if ($exists) {
                $found++;
            } else {
                $missing[] = $token;
            }
        }

        if ($found === 0 && $missing !== []) {
            throw new RuntimeException(
                'Panoplies few-shot non jouables : '.implode(', ', $missing).'.'
            );
        }
    }
}
