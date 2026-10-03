<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Entity;

use App\Models\User;
use App\Support\Entity\AccessLevelMutationGuard;
use Tests\TestCase;

/**
 * Garde SEC-07 : seuls les admins mutent read_level / write_level.
 */
class AccessLevelMutationGuardTest extends TestCase
{
    public function test_admin_keeps_access_level_keys(): void
    {
        $admin = new User(['role' => User::ROLE_ADMIN]);
        $payload = [
            'rarity' => 2,
            'read_level' => 0,
            'write_level' => 1,
        ];

        $this->assertSame($payload, AccessLevelMutationGuard::stripUnlessAdmin($admin, $payload));
        $this->assertTrue(AccessLevelMutationGuard::userMayMutate($admin));
    }

    public function test_game_master_loses_access_level_keys(): void
    {
        $gm = new User(['role' => User::ROLE_GAME_MASTER]);
        $stripped = AccessLevelMutationGuard::stripUnlessAdmin($gm, [
            'rarity' => 2,
            'read_level' => 0,
            'write_level' => 0,
        ]);

        $this->assertSame(['rarity' => 2], $stripped);
        $this->assertFalse(AccessLevelMutationGuard::userMayMutate($gm));
        $this->assertFalse(AccessLevelMutationGuard::userMayMutate(null));
    }
}
