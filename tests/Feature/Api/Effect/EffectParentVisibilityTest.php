<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Effect;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectUsage;
use App\Models\Entity\Item;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Les endpoints publics d’effets exigent `view` sur la fiche parente.
 */
final class EffectParentVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_resolve_effects_of_a_draft_spell(): void
    {
        $spell = $this->draftSpell();
        $this->attachSpellEffect($spell, 'sort-brouillon');

        $this->getJson($this->forEntityUrl('spell', $spell->id))
            ->assertForbidden();
    }

    public function test_guest_can_resolve_effects_of_a_playable_spell(): void
    {
        $spell = $this->playableSpell();
        $this->attachSpellEffect($spell, 'sort-jouable');

        $this->getJson($this->forEntityUrl('spell', $spell->id))
            ->assertOk()
            ->assertJsonPath('data.0.effect.slug', 'sort-jouable');
    }

    public function test_player_cannot_enumerate_draft_item_effects_via_for_entity(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $item = $this->draftItem();
        $this->attachItemUsage($item, 'objet-brouillon');

        $this->actingAs($player)
            ->getJson($this->forEntityUrl('item', $item->id))
            ->assertForbidden();
    }

    public function test_guest_can_resolve_effects_of_a_playable_item(): void
    {
        $item = $this->playableItem();
        $this->attachItemUsage($item, 'objet-jouable');

        $this->getJson($this->forEntityUrl('item', $item->id))
            ->assertOk()
            ->assertJsonPath('data.0.effect.slug', 'objet-jouable');
    }

    public function test_guest_cannot_list_usages_of_a_draft_item(): void
    {
        $item = $this->draftItem();
        $this->attachItemUsage($item, 'usage-brouillon');

        $this->getJson('/api/effects/usages?entity_type=item&entity_id='.$item->id)
            ->assertForbidden();
    }

    public function test_guest_can_list_usages_of_a_playable_item(): void
    {
        $item = $this->playableItem();
        $usage = $this->attachItemUsage($item, 'usage-jouable');

        $this->getJson('/api/effects/usages?entity_type=item&entity_id='.$item->id)
            ->assertOk()
            ->assertJsonPath('data.0.id', $usage->id);
    }

    public function test_guest_cannot_show_usage_of_a_draft_item(): void
    {
        $item = $this->draftItem();
        $usage = $this->attachItemUsage($item, 'usage-show-brouillon');

        $this->getJson('/api/effects/usages/'.$usage->id)
            ->assertForbidden();
    }

    public function test_game_master_can_resolve_effects_of_a_draft_spell(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spell = $this->draftSpell();
        $this->attachSpellEffect($spell, 'sort-mj');

        $this->actingAs($gm)
            ->getJson($this->forEntityUrl('spell', $spell->id))
            ->assertOk();
    }

    public function test_for_entity_returns_not_found_for_missing_parent(): void
    {
        $this->getJson($this->forEntityUrl('spell', 999_999))
            ->assertNotFound();
    }

    private function forEntityUrl(string $entityType, int $entityId): string
    {
        return '/api/effects/for-entity?entity_type='.$entityType.'&entity_id='.$entityId.'&level=1';
    }

    private function draftSpell(): Spell
    {
        return Spell::factory()->create([
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
    }

    private function playableSpell(): Spell
    {
        return Spell::factory()->create([
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
    }

    private function draftItem(): Item
    {
        return Item::factory()->create([
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
    }

    private function playableItem(): Item
    {
        return Item::factory()->create([
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
    }

    private function attachSpellEffect(Spell $spell, string $slug): EffectDegree
    {
        $degree = $this->makeDegree($slug);
        $spell->effects()->syncWithoutDetaching([$degree->effect_id]);

        return $degree;
    }

    private function attachItemUsage(Item $item, string $slug): EffectUsage
    {
        $degree = $this->makeDegree($slug);

        return EffectUsage::query()->create([
            'entity_type' => Item::class,
            'entity_id' => $item->id,
            'effect_degree_id' => $degree->id,
        ]);
    }

    private function makeDegree(string $slug): EffectDegree
    {
        $effect = Effect::query()->create([
            'name' => $slug,
            'slug' => $slug,
            'description' => 'Effet de test '.$slug,
            'target_type' => Effect::TARGET_DIRECT,
        ]);

        return EffectDegree::query()->create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'required_creature_level' => 1,
            'area' => '0',
            'slug' => $slug.'-d1',
        ]);
    }
}
