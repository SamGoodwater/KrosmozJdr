<?php

declare(strict_types=1);

namespace App\Support\Entity;

use App\Enums\EntityState;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

/**
 * Transitions d’état HTTP vs writer automatique (futur job IA).
 *
 * @example
 * EntityStateGate::authorizeHttpTransition($user, $spell, 'playable');
 * EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);
 */
final class EntityStateGate
{
    public static function isPlayable(mixed $state): bool
    {
        return is_string($state) && $state === EntityState::Playable->value;
    }

    /**
     * Publier (passer à `playable`) exige l’ability `publish`, pas seulement `update`.
     * Rester `playable` (sauvegarde d’une fiche déjà jouable) n’est pas une publication.
     */
    public static function requiresPublish(?string $from, string $to): bool
    {
        return $to === EntityState::Playable->value
            && $from !== EntityState::Playable->value;
    }

    /**
     * @throws AuthorizationException
     */
    public static function authorizeHttpTransition(?Authenticatable $user, Model $model, mixed $to): void
    {
        if (! is_string($to) || $to === '') {
            return;
        }

        $from = $model->getAttribute('state');
        $from = is_string($from) ? $from : null;
        if (! self::requiresPublish($from, $to)) {
            return;
        }

        if ($user === null || ! Gate::forUser($user)->allows('publish', $model)) {
            throw new AuthorizationException(
                'Passer une fiche en jouable exige le droit de publication (relecture).'
            );
        }
    }

    /**
     * Filet pour un futur ingest IA : uniquement `auto`, jamais `playable`.
     *
     * @throws AuthorizationException
     */
    public static function assertAutomatedWriterMaySet(string $state): void
    {
        if ($state !== EntityState::Auto->value) {
            throw new AuthorizationException(
                'Un writer automatique ne peut poser que l’état auto.'
            );
        }
    }
}
