<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Panoply;
use App\Models\Entity\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour PanoplyController
 * 
 * Vérifie que :
 * - Un utilisateur peut modifier une panoplie qu'il a créée
 * - Un admin peut modifier n'importe quelle panoplie
 * - La méthode updateItems synchronise correctement les items
 * - Les validations fonctionnent correctement
 * - Les policies fonctionnent correctement
 */
class PanoplyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Désactiver le middleware role pour les tests (on teste les policies directement)
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
    }

    /**
     * Test : Un utilisateur peut modifier une panoplie qu'il a créée
     */
    public function test_user_can_update_own_panoply(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'name' => 'Panoplie Test',
            'description' => 'Description originale',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.panoplies.edit', $panoply))
            ->patch(route('entities.panoplies.update', $panoply), [
                'name' => 'Panoplie Modifiée',
                'description' => 'Nouvelle description',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('panoplies', [
            'id' => $panoply->id,
            'name' => 'Panoplie Modifiée',
            'description' => 'Nouvelle description',
        ]);
    }

    /**
     * Test : Un utilisateur peut ajouter des items à sa panoplie
     */
    public function test_user_can_add_items_to_own_panoply(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('entities.panoplies.edit', $panoply))
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
                'items' => [$item1->id, $item2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $panoply->fresh()->items);
        $this->assertTrue($panoply->fresh()->items->contains($item1));
        $this->assertTrue($panoply->fresh()->items->contains($item2));
    }

    /**
     * Test : Un utilisateur peut retirer des items de sa panoplie
     */
    public function test_user_can_remove_items_from_own_panoply(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();
        $item3 = Item::factory()->create();
        
        // Ajouter initialement 3 items
        $panoply->items()->attach([$item1->id, $item2->id, $item3->id]);

        // Retirer item2 et item3, garder seulement item1
        $response = $this->actingAs($user)
            ->from(route('entities.panoplies.edit', $panoply))
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
                'items' => [$item1->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $panoply->fresh()->items);
        $this->assertTrue($panoply->fresh()->items->contains($item1));
        $this->assertFalse($panoply->fresh()->items->contains($item2));
        $this->assertFalse($panoply->fresh()->items->contains($item3));
    }

    /**
     * Test : Un utilisateur peut remplacer tous les items de sa panoplie
     */
    public function test_user_can_replace_all_items_in_own_panoply(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $oldItem = Item::factory()->create();
        $newItem1 = Item::factory()->create();
        $newItem2 = Item::factory()->create();
        
        // Ajouter un item initialement
        $panoply->items()->attach($oldItem->id);

        // Remplacer par de nouveaux items
        $response = $this->actingAs($user)
            ->from(route('entities.panoplies.edit', $panoply))
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
                'items' => [$newItem1->id, $newItem2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $panoply->fresh()->items);
        $this->assertFalse($panoply->fresh()->items->contains($oldItem));
        $this->assertTrue($panoply->fresh()->items->contains($newItem1));
        $this->assertTrue($panoply->fresh()->items->contains($newItem2));
    }

    /**
     * Test : Un utilisateur peut vider tous les items d'une panoplie
     */
    public function test_user_can_clear_all_items_from_own_panoply(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();
        
        // Ajouter des items initialement
        $panoply->items()->attach([$item1->id, $item2->id]);

        // Vider tous les items
        $response = $this->actingAs($user)
            ->from(route('entities.panoplies.edit', $panoply))
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
                'items' => [],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $panoply->fresh()->items);
    }

    /**
     * Test : Un admin peut modifier les items de n'importe quelle panoplie
     */
    public function test_admin_can_update_items_of_any_panoply(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.panoplies.edit', $panoply))
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
                'items' => [$item1->id, $item2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $panoply->fresh()->items);
    }

    /**
     * Test : Un utilisateur ne peut pas modifier les items d'une panoplie qu'il n'a pas créée
     */
    public function test_user_cannot_update_items_of_other_user_panoply(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $item1 = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
                'items' => [$item1->id],
            ]);

        $response->assertForbidden();
        $this->assertCount(0, $panoply->fresh()->items);
    }

    /**
     * Test : La validation échoue si items n'est pas un array
     */
    public function test_update_items_fails_if_items_is_not_array(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.panoplies.edit', $panoply))
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
                'items' => 'not-an-array',
            ]);

        $response->assertSessionHasErrors('items');
    }

    /**
     * Test : La validation échoue si un item n'existe pas
     */
    public function test_update_items_fails_if_item_does_not_exist(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $item = Item::factory()->create();
        
        // Supprimer définitivement l'item pour qu'il n'existe plus
        $item->forceDelete();

        $response = $this->actingAs($user)
            ->from(route('entities.panoplies.edit', $panoply))
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
                'items' => [$item->id],
            ]);

        $response->assertSessionHasErrors('items.0');
    }

    /**
     * Test : La validation échoue si items est manquant
     */
    public function test_update_items_fails_if_items_is_missing(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.panoplies.edit', $panoply))
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
            ]);

        $response->assertSessionHasErrors('items');
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas modifier les items
     */
    public function test_guest_cannot_update_items(): void
    {
        $panoply = Panoply::factory()->create();
        $item = Item::factory()->create();

        $response = $this->post(route('entities.panoplies.updateItems', $panoply), [
            '_method' => 'PATCH',
            'items' => [$item->id],
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : La page d'édition charge les items disponibles
     */
    public function test_edit_page_loads_available_items(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $item1 = Item::factory()->create(['name' => 'Item 1']);
        $item2 = Item::factory()->create(['name' => 'Item 2']);
        $panoply->items()->attach($item1->id);

        $response = $this->actingAs($user)
            ->get(route('entities.panoplies.edit', $panoply));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/panoply/Edit')
            ->has('panoply')
            ->has('availableItems')
            ->where('panoply.data.items.0.id', $item1->id)
        );
    }

    /**
     * Test : La synchronisation des items fonctionne avec plusieurs items
     */
    public function test_sync_items_works_with_multiple_items(): void
    {
        $user = User::factory()->create();
        $panoply = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $items = Item::factory()->count(5)->create();
        $itemIds = $items->pluck('id')->toArray();

        $response = $this->actingAs($user)
            ->from(route('entities.panoplies.edit', $panoply))
            ->post(route('entities.panoplies.updateItems', $panoply), [
                '_method' => 'PATCH',
                'items' => $itemIds,
            ]);

        $response->assertRedirect();
        $this->assertCount(5, $panoply->fresh()->items);
        foreach ($items as $item) {
            $this->assertTrue($panoply->fresh()->items->contains($item));
        }
    }
}

