<?php

namespace App\Enums;

/**
 * Enum pour les niveaux de visibilité.
 * 
 * @method static self GUEST()
 * @method static self USER()
 * @method static self GAME_MASTER()
 * @method static self ADMIN()
 */
enum Visibility: string
{
    case GUEST = 'guest';
    case USER = 'user';
    case GAME_MASTER = 'game_master';
    case ADMIN = 'admin';

    /**
     * Retourne le label traduit de la visibilité.
     * 
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::GUEST => 'Invité',
            self::USER => 'Utilisateur',
            self::GAME_MASTER => 'Maître de jeu',
            self::ADMIN => 'Administrateur',
        };
    }

    /**
     * Vérifie si un utilisateur a le niveau de visibilité requis.
     * 
     * @param \App\Models\User|null $user
     * @return bool
     */
    public function isAccessibleBy(?\App\Models\User $user): bool
    {
        if ($this === self::GUEST) {
            return true; // Toujours accessible
        }

        if (!$user) {
            return false; // Nécessite une connexion
        }

        return match($this) {
            self::GUEST => true,
            self::USER => true, // Tous les utilisateurs connectés
            self::GAME_MASTER => in_array($user->role, [
                \App\Models\User::ROLE_GAME_MASTER, 
                \App\Models\User::ROLE_ADMIN, 
                \App\Models\User::ROLE_SUPER_ADMIN,
                3, 4, 5,
                'game_master', 
                'admin', 
                'super_admin'
            ]),
            self::ADMIN => in_array($user->role, [
                \App\Models\User::ROLE_ADMIN, 
                \App\Models\User::ROLE_SUPER_ADMIN,
                4, 5,
                'admin', 
                'super_admin'
            ]),
        };
    }

    /**
     * Retourne tous les niveaux de visibilité possibles.
     * 
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    /**
     * Retourne tous les niveaux avec leurs labels.
     * 
     * @return array<string, string>
     */
    public static function toArrayWithLabels(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }
        return $result;
    }
}

