<?php

declare(strict_types=1);

namespace App\Support\Seeder;

use Illuminate\Database\Eloquent\Model;

/**
 * Mode d’écriture des seeders : création seule (défaut) ou écrasement volontaire.
 *
 * @example SeedMode::upsert(Characteristic::class, ['key' => $key], $payload);
 * @example SeedMode::forceOverwrite(true); // project:seed --overwrite
 */
final class SeedMode
{
    private static ?bool $forcedOverwrite = null;

    /**
     * True = updateOrCreate / purge d’orphelins (comportement historique).
     * False = firstOrCreate, ne pas toucher une ligne déjà présente.
     */
    public static function overwrite(): bool
    {
        if (self::$forcedOverwrite !== null) {
            return self::$forcedOverwrite;
        }

        return (bool) config('seeders.overwrite', false);
    }

    /**
     * Force le mode pour le processus courant (commandes project:seed / project:init).
     */
    public static function forceOverwrite(?bool $overwrite): void
    {
        self::$forcedOverwrite = $overwrite;
    }

    /**
     * Remet le forçage (tests).
     */
    public static function clearForce(): void
    {
        self::$forcedOverwrite = null;
    }

    /**
     * Crée ou met à jour selon {@see overwrite()}.
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $attributes  Clé de recherche
     * @param  array<string, mixed>  $values  Valeurs à écrire (création toujours ; update seulement en overwrite)
     * @return Model
     */
    public static function upsert(string $modelClass, array $attributes, array $values = []): Model
    {
        if (self::overwrite()) {
            return $modelClass::query()->updateOrCreate($attributes, $values);
        }

        return $modelClass::query()->firstOrCreate($attributes, $values);
    }

    /**
     * Indique si le modèle venait d’être créé (utile pour sync de relations).
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function wasRecentlyCreated(Model $model): bool
    {
        return (bool) $model->wasRecentlyCreated;
    }
}
