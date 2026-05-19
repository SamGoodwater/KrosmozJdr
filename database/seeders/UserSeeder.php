<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Crée ou restaure un utilisateur (soft delete) à partir d'une adresse email.
     *
     * @param  array{name:string,email:string,role:int,password:string,avatar?:string,notifications_enabled?:bool,notification_channels?:array,is_system?:bool}  $attributes
     * @param  string  $label  Label d'affichage pour la sortie console
     */
    private function createOrRestoreByEmail(array $attributes, string $label): User
    {
        $email = $attributes['email'];

        $user = User::withTrashed()->where('email', $email)->first();

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }

            $user->fill($attributes);
            $user->save();

            $this->command->info('♻️  '.$label.' restauré/mis à jour: '.$email);

            return $user;
        }

        $user = User::create($attributes);
        $this->command->info('✅ '.$label.' créé: '.$email);

        return $user;
    }

    /**
     * Ne pas peupler les comptes de démo hors environnements de développement.
     */
    private function cannotSeedTestHumans(): bool
    {
        return app()->environment('production');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utilisateur système (pour les imports automatiques, ne peut pas se connecter)
        // Note: On ne peut pas forcer l'ID à 0 avec auto-increment, mais on utilise l'email pour l'identifier
        $systemUser = $this->createOrRestoreByEmail([
            'name' => 'Système',
            'email' => User::SYSTEM_USER_EMAIL,
            'role' => User::ROLE_SUPER_ADMIN,
            'password' => Hash::make(Str::random(128)), // Mot de passe aléatoire très long (impossible à deviner)
            'avatar' => User::DEFAULT_AVATAR,
            'notifications_enabled' => false,
            'notification_channels' => [],
            'is_system' => true,
        ], 'Utilisateur système (ne peut pas se connecter)');
        $this->command->info('ℹ️  Utilisateur système ID: '.$systemUser->id);

        if ($this->cannotSeedTestHumans()) {
            $this->command->info('🎯 Environnement production : aucun compte utilisateur de test créé.');

            return;
        }

        // Compte super_admin humain : créé de façon interactive par `php artisan project:init` (ou `init`).
        $this->createOrRestoreByEmail([
            'name' => 'Test User',
            'email' => 'test-user@test.fr',
            'role' => User::ROLE_USER, // user = 1
            'password' => Hash::make('password'),
            'avatar' => User::DEFAULT_AVATAR,
            'email_verified_at' => now(),
            'notifications_enabled' => true,
            'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            'is_system' => false,
        ], 'Test User (password)');

        // Admin
        $this->createOrRestoreByEmail([
            'name' => 'Admin User',
            'email' => 'admin@test.fr',
            'role' => User::ROLE_ADMIN, // admin = 4
            'password' => Hash::make('password'),
            'avatar' => User::DEFAULT_AVATAR,
            'email_verified_at' => now(),
            'notifications_enabled' => true,
            'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            'is_system' => false,
        ], 'Admin (password)');

        // Super Admin
        $this->createOrRestoreByEmail([
            'name' => 'Super Admin User',
            'email' => 'superadmin@test.fr',
            'role' => User::ROLE_SUPER_ADMIN, // super_admin = 5
            'password' => Hash::make('password'),
            'avatar' => User::DEFAULT_AVATAR,
            'email_verified_at' => now(),
            'notifications_enabled' => true,
            'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            'is_system' => false,
        ], 'Super Admin (password)');

        // Game Master
        $this->createOrRestoreByEmail([
            'name' => 'Game Master',
            'email' => 'gm@test.fr',
            'role' => User::ROLE_GAME_MASTER, // game_master = 3
            'password' => Hash::make('password'),
            'avatar' => User::DEFAULT_AVATAR,
            'email_verified_at' => now(),
            'notifications_enabled' => true,
            'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            'is_system' => false,
        ], 'Game Master (password)');

        // Player
        $this->createOrRestoreByEmail([
            'name' => 'Player User',
            'email' => 'player@test.fr',
            'role' => User::ROLE_PLAYER, // player = 2
            'password' => Hash::make('password'),
            'avatar' => User::DEFAULT_AVATAR,
            'email_verified_at' => now(),
            'notifications_enabled' => true,
            'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            'is_system' => false,
        ], 'Player (password)');

        $this->command->info('🎯 Tous les utilisateurs de test ont été créés avec succès !');
    }
}
