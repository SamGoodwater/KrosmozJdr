<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Capability;
use App\Models\User;
use Tests\TestCase;

/**
 * Vérifie que la mise à jour HTTP normalise les champs NOT NULL (ex. saisie vide → null en prod).
 */
class CapabilityControllerUpdateTest extends TestCase
{
    public function test_update_accepts_null_time_before_use_again_and_persists_db_default(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $capability = Capability::factory()->create([
            'time_before_use_again' => '1 fois par combat',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->patchJson(
            '/entities/capabilities/'.$capability->id,
            [
                'name' => $capability->name,
                'time_before_use_again' => null,
                'ritual_available' => null,
            ]
        );

        $response->assertRedirect();
        $capability->refresh();
        $this->assertSame('0', $capability->time_before_use_again);
        $this->assertTrue($capability->ritual_available);
    }
}
