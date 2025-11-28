<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Database\Factories\UserFactory;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utilisateur système (pour les imports automatiques, ne peut pas se connecter)
        if (!User::where('email', User::SYSTEM_USER_EMAIL)->exists()) {
            // Créer l'utilisateur système
            // Note: On ne peut pas forcer l'ID à 0 avec auto-increment, mais on utilise l'email pour l'identifier
            $systemUser = User::create([
                'name' => 'Système',
                'email' => User::SYSTEM_USER_EMAIL,
                'role' => User::ROLE_SUPER_ADMIN,
                'password' => Hash::make(Str::random(128)), // Mot de passe aléatoire très long (impossible à deviner)
                'avatar' => User::DEFAULT_AVATAR,
                'notifications_enabled' => false,
                'notification_channels' => [],
                'is_system' => true,
            ]);
            $this->command->info('✅ Utilisateur système créé (ID: ' . $systemUser->id . ', ne peut pas se connecter)');
        }

        // Super Admin
        if (!User::where('email', 'super-admin@test.fr')->exists()) {
            User::factory()->create([
                'name' => 'Super Admin',
                'email' => 'super-admin@test.fr',
                'role' => User::ROLE_SUPER_ADMIN, // super_admin = 5
                'password' => Hash::make('0000'),
                'avatar' => User::DEFAULT_AVATAR,
                'notifications_enabled' => true,
                'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            ]);
            $this->command->info('✅ Super Admin créé: super-admin@test.fr / 0000');
        }

        // Test User
        if (!User::where('email', 'test-user@test.fr')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test-user@test.fr',
                'role' => User::ROLE_USER, // user = 1
                'password' => Hash::make('password'),
                'avatar' => User::DEFAULT_AVATAR,
                'notifications_enabled' => true,
                'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            ]);
            $this->command->info('✅ Test User créé: test-user@test.fr / password');
        }

        // Admin
        if (!User::where('email', 'admin@test.fr')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@test.fr',
                'role' => User::ROLE_ADMIN, // admin = 4
                'password' => Hash::make('password'),
                'avatar' => User::DEFAULT_AVATAR,
                'notifications_enabled' => true,
                'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            ]);
            $this->command->info('✅ Admin créé: admin@test.fr / password');
        }

        // Game Master
        if (!User::where('email', 'gm@test.fr')->exists()) {
            User::factory()->create([
                'name' => 'Game Master',
                'email' => 'gm@test.fr',
                'role' => User::ROLE_GAME_MASTER, // game_master = 3
                'password' => Hash::make('password'),
                'avatar' => User::DEFAULT_AVATAR,
                'notifications_enabled' => true,
                'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            ]);
            $this->command->info('✅ Game Master créé : gm@test.fr / password');
        }

        // Player
        if (!User::where('email', 'player@test.fr')->exists()) {
            User::factory()->create([
                'name' => 'Player User',
                'email' => 'player@test.fr',
                'role' => User::ROLE_PLAYER, // player = 2
                'password' => Hash::make('password'),
                'avatar' => User::DEFAULT_AVATAR,
                'notifications_enabled' => true,
                'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
            ]);
            $this->command->info('✅ Player créé: player@test.fr / password');
        }

        $this->command->info('🎯 Tous les utilisateurs de test ont été créés avec succès !');
    }
}
