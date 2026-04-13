<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour CharacteristicController
 *
 * @description
 * Vérifie que GET /api/characteristics retourne la structure attendue
 * (creature, spell, capability, item, consumable, resource, panoply).
 */
class CharacteristicControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test : GET /api/characteristics retourne la structure complète
     */
    public function test_index_returns_expected_structure(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/characteristics');

        $response->assertOk();

        $data = $response->json();
        $this->assertIsArray($data);

        $expectedGroups = ['creature', 'spell', 'capability', 'item', 'consumable', 'resource', 'panoply'];
        foreach ($expectedGroups as $group) {
            $this->assertArrayHasKey($group, $data, "Groupe manquant : {$group}");
        }

        $this->assertArrayHasKey('byMonsterField', $data['creature']);
        $this->assertIsArray($data['creature']['byMonsterField']);

        if (isset($data['creature']['byDbColumn'])) {
            $this->assertIsArray($data['creature']['byDbColumn']);
            if (count($data['creature']['byDbColumn']) > 0) {
                $firstKey = array_key_first($data['creature']['byDbColumn']);
                $first = $data['creature']['byDbColumn'][$firstKey];
                $this->assertArrayHasKey('key', $first);
                $this->assertArrayHasKey('name', $first);
            }
        }
    }

    /**
     * Test : GET /api/characteristics sans auth (si public)
     */
    public function test_index_accessible_without_auth(): void
    {
        $response = $this->getJson('/api/characteristics');

        $response->assertOk();
    }
}
