<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\OAuthAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\User\UserPolicyTest;
use Tests\TestCase;
use Tests\Unit\UserTest;

/**
 * Garde-fous web : compte système non connectable, OAuth sans liaison au compte technique.
 *
 * Ces scénarios complètent {@see UserPolicyTest} et {@see UserTest}.
 * Inclus dans la suite PHPUnit lancée par `project:review --test-back` / `--tests` / `--all`.
 */
#[Group('security')]
class SystemAccountAndSuperAdminWebTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Même identifiants valides : la session ne doit pas s’ouvrir (compte `is_system`).
     */
    public function test_system_user_cannot_authenticate_via_login_form(): void
    {
        $user = User::factory()->create([
            'email' => 'system-web-guard@example.test',
            'is_system' => true,
            'password' => 'correct-password',
        ]);

        $response = $this->post(route('login'), [
            'identifier' => $user->email,
            'password' => 'correct-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['identifier']);
    }

    /**
     * Liaison OAuth refusée avant création d’enregistrement (pas de compte technique « connectable »).
     */
    public function test_oauth_confirm_link_does_not_attach_provider_to_system_user(): void
    {
        $system = User::factory()->create([
            'email' => 'oauth-blocked-system@example.test',
            'is_system' => true,
        ]);

        $pending = [
            'provider' => 'github',
            'provider_id' => 'gh-test-'.$system->id,
            'provider_email' => $system->email,
            'provider_name' => 'Git User',
            'avatar_url' => null,
            'existing_user_id' => $system->id,
        ];

        $response = $this->withSession(['oauth.pending_link' => $pending])
            ->post(route('oauth.confirm-link.post'), []);

        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('error');

        $this->assertSame(
            0,
            OAuthAccount::query()->where('user_id', $system->id)->count(),
            'Aucune ligne OAuth ne doit être créée pour un compte système refusé.'
        );
    }
}
