<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Monster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreatureExpertiseValidationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function monster_update_rejects_too_many_expertises_for_level(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = Monster::factory()->create();
        $creature = $monster->creature;
        $this->assertNotNull($creature);
        $creature->update(['level' => '12']);

        $this->actingAs($admin)
            ->from(route('entities.monsters.edit', $monster))
            ->patch(route('entities.monsters.update', $monster), [
                'athletisme_mastery' => 2,
                'discretion_mastery' => 2,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('mastery');
    }
}
