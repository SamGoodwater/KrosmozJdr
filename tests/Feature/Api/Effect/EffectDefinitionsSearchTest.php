<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Effect;

use App\Http\Middleware\CheckRole;
use App\Models\Effect;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Recherche légère de définitions d’effet pour la liaison sort.
 */
final class EffectDefinitionsSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_guest_cannot_search_definitions(): void
    {
        $this->getJson('/api/effects/definitions?q=feu')
            ->assertUnauthorized();
    }

    public function test_author_can_search_and_exclude_linked_definitions(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create(['created_by' => $author->id]);

        $linked = Effect::create([
            'name' => 'Boule de Feu',
            'slug' => 'boule-feu',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $free = Effect::create([
            'name' => 'Flamme libre',
            'slug' => 'flamme-libre',
            'target_type' => Effect::TARGET_DIRECT,
        ]);
        $spell->effects()->attach($linked->id);

        $response = $this->actingAs($author)
            ->getJson('/api/effects/definitions?'.http_build_query([
                'q' => 'Flam',
                'limit' => 20,
                'exclude_spell_id' => $spell->id,
            ]));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('effect_definition_id')->all();
        $this->assertContains($free->id, $ids);
        $this->assertNotContains($linked->id, $ids);
    }

    public function test_exclude_spell_id_forbidden_for_non_author(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);
        $spell = Spell::factory()->create(['created_by' => $owner->id]);

        $this->actingAs($other)
            ->getJson('/api/effects/definitions?exclude_spell_id='.$spell->id)
            ->assertForbidden();
    }
}
