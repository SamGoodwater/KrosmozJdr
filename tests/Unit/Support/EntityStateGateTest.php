<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Enums\EntityState;
use App\Http\Middleware\PreventRequestForgery;
use App\Models\Entity\Item;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Support\Entity\EntityStateGate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntityStateGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_publish_only_when_entering_playable(): void
    {
        $this->assertTrue(EntityStateGate::requiresPublish('draft', EntityState::Playable->value));
        $this->assertTrue(EntityStateGate::requiresPublish('auto', EntityState::Playable->value));
        $this->assertTrue(EntityStateGate::requiresPublish(null, EntityState::Playable->value));
        $this->assertFalse(EntityStateGate::requiresPublish('playable', EntityState::Playable->value));
        $this->assertFalse(EntityStateGate::requiresPublish('raw', EntityState::Auto->value));
    }

    public function test_automated_writer_may_only_set_auto(): void
    {
        EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);

        $this->expectException(AuthorizationException::class);
        EntityStateGate::assertAutomatedWriterMaySet(EntityState::Playable->value);
    }

    public function test_csrf_middleware_has_no_scrapping_exception(): void
    {
        $ref = new \ReflectionClass(PreventRequestForgery::class);
        $except = $ref->getProperty('except');
        $instance = $ref->newInstanceWithoutConstructor();

        $this->assertSame([], $except->getValue($instance));
        $this->assertStringNotContainsString(
            "'api/scrapping'",
            (string) file_get_contents(base_path('bootstrap/app.php'))
        );
    }

    /**
     * MJ peut publier un Item (`publish` = `updateAny` = isGameMaster).
     */
    public function test_authorize_http_transition_allows_user_with_publish(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $item = Item::factory()->create(['state' => Item::STATE_DRAFT]);

        EntityStateGate::authorizeHttpTransition($gm, $item, Item::STATE_PLAYABLE);

        $this->addToAssertionCount(1);
    }

    /**
     * MJ ne peut pas publier un Spell (admin-only via `updateAny`).
     */
    public function test_authorize_http_transition_denies_user_without_publish(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = Spell::factory()->create(['state' => Spell::STATE_DRAFT]);

        $this->expectException(AuthorizationException::class);
        EntityStateGate::authorizeHttpTransition($gm, $spell, Spell::STATE_PLAYABLE);
    }

    public function test_authorize_http_transition_denies_null_user(): void
    {
        $spell = Spell::factory()->create(['state' => Spell::STATE_DRAFT]);

        $this->expectException(AuthorizationException::class);
        EntityStateGate::authorizeHttpTransition(null, $spell, Spell::STATE_PLAYABLE);
    }

    /**
     * Rester `playable` n’est pas une publication : no-op même sans utilisateur.
     */
    public function test_authorize_http_transition_noop_when_staying_playable(): void
    {
        $spell = Spell::factory()->create(['state' => Spell::STATE_PLAYABLE]);

        EntityStateGate::authorizeHttpTransition(null, $spell, Spell::STATE_PLAYABLE);

        $this->addToAssertionCount(1);
    }
}
