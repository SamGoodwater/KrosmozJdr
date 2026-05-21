<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\GlobalSearchController;
use App\Models\Entity\Spell;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour {@see GlobalSearchController}.
 *
 * @description
 * Vérifie la réponse JSON, les filtres `types` / `states`, et le respect du Gate `view`.
 */
class GlobalSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Une requête trop courte renvoie une liste vide (pas d’erreur).
     */
    public function test_short_query_returns_empty_results(): void
    {
        $response = $this->getJson('/api/global-search?q=x');

        $response->assertOk()
            ->assertJson([
                'results' => [],
                'meta' => [
                    'hasMore' => false,
                ],
            ]);
    }

    /**
     * Invité : sort jouable visible dans les résultats lorsque la policy le permet.
     */
    public function test_guest_sees_matching_playable_spell_when_policy_allows(): void
    {
        $token = 'GlSearchSpellTok'.uniqid();

        Spell::factory()->create([
            'name' => 'Sort '.$token,
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['spells'],
            'limit' => 10,
        ]));

        $response->assertOk();
        $results = $response->json('results');
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        $hit = collect($results)->firstWhere('entityType', 'spells');
        $this->assertNotNull($hit);
        $this->assertSame('Sorts', $hit['group']);
        $this->assertStringContainsString($token, (string) $hit['title']);
        $this->assertNotEmpty($hit['href']);
    }

    /**
     * Le paramètre `types[]` limite aux types demandés (requête SQL), sans traiter les autres tables.
     */
    public function test_types_query_limits_search_scope(): void
    {
        $token = 'GlSearchTypesTok'.uniqid();

        Page::factory()->create([
            'title' => 'Page '.$token,
            'slug' => 'page-'.md5($token),
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);

        Spell::factory()->create([
            'name' => 'Sort '.$token,
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['pages'],
        ]));

        $response->assertOk();
        $results = $response->json('results');
        $this->assertCount(1, $results);
        $this->assertSame('pages', $results[0]['entityType']);
    }

    /**
     * Le paramètre `states[]` filtre bien les lignes avant application du Gate.
     */
    public function test_states_query_filters_rows(): void
    {
        $token = 'GlSearchStatesTok'.uniqid();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Spell::factory()->create([
            'name' => 'Alpha '.$token,
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        Spell::factory()->create([
            'name' => 'Beta '.$token,
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $responsePlayable = $this->actingAs($admin)->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['spells'],
            'states' => ['playable'],
        ]));
        $responsePlayable->assertOk();
        $titlesPlayable = collect($responsePlayable->json('results'))->pluck('title')->all();
        $this->assertTrue(collect($titlesPlayable)->contains(fn ($t) => str_contains((string) $t, 'Alpha')));
        $this->assertFalse(collect($titlesPlayable)->contains(fn ($t) => str_contains((string) $t, 'Beta')));

        $responseDraft = $this->actingAs($admin)->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['spells'],
            'states' => ['draft'],
        ]));
        $responseDraft->assertOk();
        $titlesDraft = collect($responseDraft->json('results'))->pluck('title')->all();
        $this->assertFalse(collect($titlesDraft)->contains(fn ($t) => str_contains((string) $t, 'Alpha')));
        $this->assertTrue(collect($titlesDraft)->contains(fn ($t) => str_contains((string) $t, 'Beta')));
    }

    /**
     * Structure minimale attendue pour chaque résultat.
     */
    public function test_each_hit_has_expected_keys(): void
    {
        $token = 'GlSearchStructTok'.uniqid();

        Spell::factory()->create([
            'name' => 'Sort '.$token,
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['spells'],
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'results' => [
                '*' => [
                    'id',
                    'entityType',
                    'group',
                    'title',
                    'subtitle',
                    'href',
                    'icon',
                    'iconUrl',
                ],
            ],
            'meta' => [
                'limit',
                'hasMore',
            ],
        ]);
    }

    /**
     * `meta.hasMore` est vrai lorsque plus de lignes existent que la limite demandée.
     */
    /**
     * Invité : un sort en brouillon ne doit pas apparaître même si le nom correspond.
     */
    public function test_guest_does_not_see_draft_spell_in_results(): void
    {
        $token = 'GlSearchGuestDraftTok'.uniqid();

        Spell::factory()->create([
            'name' => 'Brouillon '.$token,
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['spells'],
        ]));

        $response->assertOk();
        $this->assertSame([], $response->json('results'));
    }

    public function test_meta_has_more_when_results_exceed_limit(): void
    {
        $token = 'GlSearchMoreTok'.uniqid();

        for ($i = 0; $i < 3; $i++) {
            Spell::factory()->create([
                'name' => "Sort {$i} {$token}",
                'state' => Spell::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_GAME_MASTER,
            ]);
        }

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['spells'],
            'limit' => 2,
        ]));

        $response->assertOk();
        $this->assertCount(2, $response->json('results'));
        $this->assertTrue($response->json('meta.hasMore'));
    }
}
