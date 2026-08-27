<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\GlobalSearchController;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Page;
use App\Models\Type\ResourceType;
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
     * Les hits exposent iconUrl lorsque l’entité a une image (pas de requête média supplémentaire).
     */
    public function test_hit_includes_icon_url_from_entity_image(): void
    {
        $token = 'GlSearchImgTok'.uniqid();
        $imageUrl = 'https://cdn.example.test/spells/'.$token.'.png';

        Spell::factory()->create([
            'name' => 'Sort '.$token,
            'image' => $imageUrl,
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
        $hit = collect($response->json('results'))->firstWhere('entityType', 'spells');
        $this->assertNotNull($hit);
        $this->assertSame($imageUrl, $hit['iconUrl']);
        $this->assertSame('', $hit['icon']);
    }

    /**
     * Monstre : miniature via creature.image (relation déjà eager-loadée).
     */
    public function test_monster_hit_uses_creature_image(): void
    {
        $token = 'GlSearchMonImg'.uniqid();
        $imageUrl = 'https://cdn.example.test/monsters/'.$token.'.png';

        $creature = Creature::factory()->create([
            'name' => 'Créature '.$token,
            'image' => $imageUrl,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        Monster::factory()->create([
            'creature_id' => $creature->id,
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['monsters'],
            'limit' => 10,
        ]));

        $response->assertOk();
        $hit = collect($response->json('results'))->firstWhere('entityType', 'monsters');
        $this->assertNotNull($hit);
        $this->assertSame($imageUrl, $hit['iconUrl']);
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
     * Le filtre `states[]=auto` est accepté et ne remonte que les fiches Auto.
     */
    public function test_auto_state_filter_is_accepted(): void
    {
        $token = 'GlSearchAutoTok'.uniqid();

        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        Spell::factory()->create([
            'name' => 'Jouable '.$token,
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        Spell::factory()->create([
            'name' => 'Auto '.$token,
            'state' => Spell::STATE_AUTO,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->actingAs($gm)->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['spells'],
            'states' => ['auto'],
        ]));

        $response->assertOk();
        $titles = collect($response->json('results'))->pluck('title')->all();
        $this->assertTrue(collect($titles)->contains(fn ($t) => str_contains((string) $t, 'Auto')));
        $this->assertFalse(collect($titles)->contains(fn ($t) => str_contains((string) $t, 'Jouable')));
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

    /**
     * Le titre d’un monstre utilise le nom de la créature liée (pas l’id seul).
     */
    public function test_monster_title_uses_creature_name(): void
    {
        $token = 'GlSearchMonsterTok'.uniqid();
        $creatureName = 'Bouftou '.$token;

        $creature = Creature::factory()->create([
            'name' => $creatureName,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        Monster::factory()->create([
            'creature_id' => $creature->id,
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['monsters'],
        ]));

        $response->assertOk();
        $hit = collect($response->json('results'))->firstWhere('entityType', 'monsters');
        $this->assertNotNull($hit);
        $this->assertSame($creatureName, $hit['title']);
        $this->assertStringNotContainsString('#', (string) $hit['title']);
    }

    /**
     * Le type `creatures` n’est plus indexé par la recherche globale.
     */
    public function test_creatures_type_is_not_searchable(): void
    {
        $token = 'GlSearchCreatureTok'.uniqid();

        Creature::factory()->create([
            'name' => 'Créature '.$token,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['creatures'],
        ]));

        $response->assertOk();
        $this->assertSame([], $response->json('results'));
    }

    /**
     * Un type de ressource redirige vers l’index ressources filtré.
     */
    public function test_resource_type_href_points_to_filtered_resources_index(): void
    {
        $token = 'GlSearchResTypeTok'.uniqid();

        $resourceType = ResourceType::factory()->create([
            'name' => 'Minerai '.$token,
            'state' => ResourceType::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['resource-types'],
        ]));

        $response->assertOk();
        $hit = collect($response->json('results'))->firstWhere('entityType', 'resource-types');
        $this->assertNotNull($hit);
        $this->assertStringContainsString('resource_type_id='.$resourceType->id, (string) $hit['href']);
        $this->assertStringContainsString(route('entities.resources.index', [], false), (string) $hit['href']);
    }

    /**
     * Libellé de groupe « Équipements » pour les items.
     */
    public function test_item_group_label_is_equipements(): void
    {
        $token = 'GlSearchItemLblTok'.uniqid();

        Item::factory()->create([
            'name' => 'Épée '.$token,
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['items'],
        ]));

        $response->assertOk();
        $hit = collect($response->json('results'))->firstWhere('entityType', 'items');
        $this->assertNotNull($hit);
        $this->assertSame('Équipements', $hit['group']);
    }

    /**
     * Fiche show équipement : page Inertia rendue pour un invité autorisé.
     */
    public function test_item_show_page_renders_for_guest(): void
    {
        $item = Item::factory()->create([
            'name' => 'Item show test',
            'state' => Item::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->get(route('entities.items.show', $item));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/item/Show')
            ->has('item.data')
            ->where('item.data.id', $item->id)
        );
    }

    /**
     * Fiche show consommable : page Inertia rendue pour un invité autorisé.
     */
    public function test_consumable_show_page_renders_for_guest(): void
    {
        $consumable = Consumable::factory()->create([
            'name' => 'Consumable show test',
            'state' => Consumable::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->get(route('entities.consumables.show', $consumable));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/consumable/Show')
            ->has('consumable.data')
            ->where('consumable.data.id', $consumable->id)
        );
    }
}
