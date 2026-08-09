<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\UserFavorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_manage_favorites(): void
    {
        $this->getJson(route('api.favorites.index'))->assertUnauthorized();
        $this->postJson(route('api.favorites.store'), [
            'entity_type' => 'spells',
            'entity_id' => 1,
        ])->assertUnauthorized();
    }

    public function test_user_can_crud_favorites(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $create = $this->postJson(route('api.favorites.store'), [
            'entity_type' => 'spells',
            'entity_id' => 42,
        ]);
        $create->assertCreated();
        $create->assertJsonPath('favorited', true);

        $this->assertDatabaseHas('user_favorites', [
            'user_id' => $user->id,
            'entity_type' => 'spells',
            'entity_id' => 42,
        ]);

        $idempotent = $this->postJson(route('api.favorites.store'), [
            'entity_type' => 'spells',
            'entity_id' => 42,
        ]);
        $idempotent->assertOk();
        $this->assertSame(1, UserFavorite::query()->where('user_id', $user->id)->count());

        $index = $this->getJson(route('api.favorites.index', ['hydrate' => 0]));
        $index->assertOk();
        $index->assertJsonPath('count', 1);
        $index->assertJsonPath('ids_by_type.spells.0', '42');

        $delete = $this->deleteJson(route('api.favorites.destroy'), [
            'entity_type' => 'spells',
            'entity_id' => 42,
        ]);
        $delete->assertOk()->assertJsonPath('favorited', false);

        $this->assertDatabaseMissing('user_favorites', [
            'user_id' => $user->id,
            'entity_type' => 'spells',
            'entity_id' => 42,
        ]);
    }

    public function test_rejects_unknown_entity_type(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route('api.favorites.store'), [
            'entity_type' => 'not-a-type',
            'entity_id' => 1,
        ])->assertStatus(422);
    }
}
