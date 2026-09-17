<?php

namespace App\Support;

/**
 * Configuration OAuth : providers activés selon les credentials .env.
 *
 * Un provider est considéré activé si ses credentials requis sont renseignés.
 */
class OAuthConfig
{
    /**
     * Retourne la liste des providers OAuth activés (credentials configurés).
     *
     * @return list<string>
     */
    public static function enabledProviders(): array
    {
        $enabled = [];

        if (self::isGitHubEnabled()) {
            $enabled[] = 'github';
        }
        if (self::isDiscordEnabled()) {
            $enabled[] = 'discord';
        }
        if (self::isSteamEnabled()) {
            $enabled[] = 'steam';
        }

        return $enabled;
    }

    public static function isGitHubEnabled(): bool
    {
        return ! empty(config('services.github.client_id'))
            && ! empty(config('services.github.client_secret'));
    }

    public static function isDiscordEnabled(): bool
    {
        return ! empty(config('services.discord.client_id'))
            && ! empty(config('services.discord.client_secret'));
    }

    public static function isSteamEnabled(): bool
    {
        return ! empty(config('services.steam.client_secret'));
    }

    /**
     * Vérifie si un provider est activé.
     */
    public static function isProviderEnabled(string $provider): bool
    {
        return in_array($provider, self::enabledProviders(), true);
    }
}
