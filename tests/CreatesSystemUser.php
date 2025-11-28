<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Trait pour créer l'utilisateur système dans les tests
 * 
 * @package Tests
 */
trait CreatesSystemUser
{
    /**
     * Crée l'utilisateur système si il n'existe pas
     * 
     * @return User
     */
    protected function createSystemUser(): User
    {
        $systemUser = User::getSystemUser();
        
        if (!$systemUser) {
            $systemUser = User::create([
                'name' => 'Système',
                'email' => User::SYSTEM_USER_EMAIL,
                'role' => User::ROLE_SUPER_ADMIN,
                'password' => Hash::make(Str::random(128)),
                'avatar' => User::DEFAULT_AVATAR,
                'notifications_enabled' => false,
                'notification_channels' => [],
                'is_system' => true,
            ]);
        }
        
        return $systemUser;
    }
}

