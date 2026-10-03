<?php

declare(strict_types=1);

namespace App\Services\Creature;

use App\Support\Creature\CreatureMasteryColumns;

/**
 * Valide les paliers de maîtrise / expertise (0–2) sur une fiche créature.
 *
 * Règles : max 3 expertises (palier 2) ; paliers de niveau 9 / 15 / 20 ; niveau < 9 interdit l'expertise.
 *
 * @example
 *   $errors = $validator->validate(12, CreatureMasteryColumns::extractFrom($creature));
 */
final class CreatureExpertiseValidator
{
    /**
     * @param  array<string, int>  $masteryByColumn
     * @return list<string>
     */
    public function validate(int $level, array $masteryByColumn): array
    {
        $errors = [];
        $expertiseCount = $this->countExpertises($masteryByColumn);

        if ($level < 9 && $expertiseCount > 0) {
            $errors[] = 'Aucune expertise (palier 2) n’est autorisée avant le niveau 9.';
        }

        $allowed = $this->maxExpertisesForLevel($level);
        if ($expertiseCount > $allowed) {
            $errors[] = sprintf(
                'Maximum %d expertise(s) autorisée(s) au niveau %d (actuellement %d).',
                $allowed,
                $level,
                $expertiseCount
            );
        }

        if ($expertiseCount > 3) {
            $errors[] = 'Maximum 3 expertises au total.';
        }

        foreach ($masteryByColumn as $column => $tier) {
            if ((int) $tier === 2 && $level < 9) {
                $errors[] = sprintf('Expertise interdite sur %s avant le niveau 9.', $column);
            }
        }

        return $errors;
    }

    /**
     * Nombre d’expertises (palier 2) autorisées selon le niveau (paliers 9 / 15 / 20).
     */
    public function maxExpertisesForLevel(int $level): int
    {
        if ($level < 9) {
            return 0;
        }
        if ($level < 15) {
            return 1;
        }
        if ($level < 20) {
            return 2;
        }

        return 3;
    }

    /**
     * @param  array<string, int>  $masteryByColumn
     */
    public function countExpertises(array $masteryByColumn): int
    {
        $count = 0;
        foreach (CreatureMasteryColumns::all() as $column) {
            if ((int) ($masteryByColumn[$column] ?? 0) === 2) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Fusionne l’état courant de la créature avec les maîtrises présentes dans la requête.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, int>
     */
    public function mergeMasteryInput(object $creature, array $input): array
    {
        $merged = CreatureMasteryColumns::extractFrom($creature);
        foreach (CreatureMasteryColumns::all() as $column) {
            if (array_key_exists($column, $input)) {
                $merged[$column] = (int) $input[$column];
            }
        }

        return $merged;
    }

    /**
     * Niveau effectif pour la validation (entier ≥ 1).
     */
    public function resolveLevel(object $creature, array $input): int
    {
        $raw = $input['level'] ?? $creature->level ?? 1;
        if (is_numeric($raw)) {
            return max(1, (int) $raw);
        }

        return max(1, (int) ($creature->level ?? 1));
    }
}
