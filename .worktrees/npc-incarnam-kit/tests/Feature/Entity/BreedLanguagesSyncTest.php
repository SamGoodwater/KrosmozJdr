<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Breed;
use App\Models\Entity\Language;
use App\Models\Entity\Monster;
use App\Models\User;
use Tests\TestCase;

class BreedLanguagesSyncTest extends TestCase
{
    public function test_admin_can_sync_breed_languages_with_sort_order(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $l1 = Language::factory()->create();
        $l2 = Language::factory()->create();

        $this->actingAs($admin)
            ->patch(route('entities.breeds.updateLanguages', $breed), [
                'languages' => [$l2->id, $l1->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('breed_language', [
            'breed_id' => $breed->id,
            'language_id' => $l2->id,
            'sort_order' => 0,
        ]);
        $this->assertDatabaseHas('breed_language', [
            'breed_id' => $breed->id,
            'language_id' => $l1->id,
            'sort_order' => 1,
        ]);
    }

    public function test_admin_can_sync_monster_languages_with_sort_order(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create([
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $l1 = Language::factory()->create();
        $l2 = Language::factory()->create();

        $this->actingAs($admin)
            ->patch(route('entities.monsters.updateLanguages', $monster), [
                'languages' => [$l2->id, $l1->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('monster_language', [
            'monster_id' => $monster->id,
            'language_id' => $l2->id,
            'sort_order' => 0,
        ]);
        $this->assertDatabaseHas('monster_language', [
            'monster_id' => $monster->id,
            'language_id' => $l1->id,
            'sort_order' => 1,
        ]);
    }
}
