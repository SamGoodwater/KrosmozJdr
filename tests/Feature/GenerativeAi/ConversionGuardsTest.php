<?php

declare(strict_types=1);

namespace Tests\Feature\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class ConversionGuardsTest extends TestCase
{
    use FakesAnthropicJson;

    public function test_mismatched_action_does_not_write_another_entity(): void
    {
        $this->fakeAnthropicJson(['effect' => 'ne doit pas être écrit']);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableSpell();
        $spell = Spell::factory()->create([
            'name' => 'Sort à préserver',
            'effect' => 'original',
            'state' => EntityState::Playable->value,
        ]);
        $monster = Monster::factory()->create([
            'state' => EntityState::Raw->value,
        ]);
        $monster->creature?->update(['state' => EntityState::Raw->value]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'spell',
            ])
            ->assertStatus(422);

        $this->assertSame('original', $spell->fresh()->effect);
        $this->assertSame(EntityState::Playable->value, $spell->fresh()->state);
        $this->assertSame(EntityState::Raw->value, $monster->fresh()->state);
        Http::assertSentCount(0);
    }

    public function test_playable_spell_requires_force(): void
    {
        $this->fakeAnthropicJson(['effect' => 'écrasé']);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableSpell();
        $spell = Spell::factory()->create([
            'name' => 'Étalon cible',
            'effect' => '1d6 Terre',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'spells', 'id' => $spell->id]), [
                'action' => 'spell',
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Fiche jouable ou archivée : passez force=true (ou --force) pour écraser le contenu.');

        $this->assertSame('1d6 Terre', $spell->fresh()->effect);
        $this->assertSame(EntityState::Playable->value, $spell->fresh()->state);
        Http::assertSentCount(0);
    }

    public function test_playable_spell_converts_with_force(): void
    {
        $this->fakeAnthropicJson(['effect' => '1d8 Terre']);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableSpell();
        $spell = Spell::factory()->create([
            'name' => 'Étalon forcé',
            'effect' => 'ancien',
            'state' => EntityState::Playable->value,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'spells', 'id' => $spell->id]), [
                'action' => 'spell',
                'force' => true,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame('1d8 Terre', $spell->fresh()->effect);
        $this->assertSame(EntityState::Auto->value, $spell->fresh()->state);
        Http::assertSentCount(1);
    }

    public function test_artisan_playable_requires_force_flag(): void
    {
        $this->fakeAnthropicJson(['effect' => 'ne doit pas passer']);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableSpell();
        $spell = Spell::factory()->create([
            'name' => 'CLI playable',
            'effect' => 'gardé',
            'state' => EntityState::Playable->value,
        ]);

        $this->artisan('ia:convert', [
            'type' => 'spell',
            '--id' => $spell->id,
            '--user' => $admin->id,
        ])->assertFailed();

        $this->assertSame('gardé', $spell->fresh()->effect);
        $this->assertSame(EntityState::Playable->value, $spell->fresh()->state);
        Http::assertSentCount(0);
    }

    public function test_playable_monster_requires_force(): void
    {
        $this->fakeAnthropicJson([
            'monster' => [],
            'spells' => [
                ['name' => 'Bec', 'effect' => '1d6 Air', 'pa' => '3', 'element' => 'air'],
                ['name' => 'Picore', 'effect' => '1d4 Air', 'pa' => '2', 'element' => 'air'],
            ],
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Monster::factory()->create([
            'official_id' => 'jdr:bestiary:piou-vert',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);
        $monster = Monster::factory()->create([
            'official_id' => 'dofus:playable-source',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);
        $monster->creature?->update(['state' => EntityState::Playable->value, 'pa' => '6']);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertSame(EntityState::Playable->value, $monster->fresh()->state);
        Http::assertSentCount(0);
    }

    public function test_playable_creature_on_raw_monster_requires_force(): void
    {
        $this->fakeAnthropicJson([
            'monster' => [],
            'spells' => [
                ['name' => 'Bec', 'effect' => '1d6 Air', 'pa' => '3'],
                ['name' => 'Picore', 'effect' => '1d4 Air', 'pa' => '2'],
            ],
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Monster::factory()->create([
            'official_id' => 'jdr:bestiary:piou-vert',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);
        $monster = Monster::factory()->create([
            'official_id' => 'dofus:encounter-source',
            'state' => EntityState::Raw->value,
            'auto_update' => true,
        ]);
        $monster->creature?->update([
            'pa' => '6',
            'state' => EntityState::Playable->value,
        ]);

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertStatus(422);

        $this->assertSame(EntityState::Raw->value, $monster->fresh()->state);
        $this->assertSame(EntityState::Playable->value, $monster->fresh()->creature?->state);
        Http::assertSentCount(0);
    }

    private function seedPlayableSpell(): Spell
    {
        return Spell::factory()->create([
            'name' => 'Pression',
            'official_id' => 'jdr:spell:pression',
            'effect' => '1d6 + Force (Terre).',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);
    }
}
