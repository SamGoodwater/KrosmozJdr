<?php

declare(strict_types=1);

namespace Tests\Feature\PagesSections;

use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * API d’aperçu minimal pour références kref « entité ».
 *
 * @example php artisan test --filter=CmsKrefEntityPreviewApiTest
 */
class CmsKrefEntityPreviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_kref_entity_preview_returns_payload_for_guest(): void
    {
        $spell = Spell::factory()->create([
            'name' => 'Boule de feu test',
            'level' => '5',
            'pa' => '4',
            'read_level' => User::ROLE_GUEST,
        ]);

        $res = $this->getJson(route('api.cms.kref-entity-preview', [
            'entityType' => 'spells',
            'id' => $spell->id,
        ]));

        $res->assertOk()
            ->assertJsonPath('entityType', 'spells')
            ->assertJsonPath('name', 'Boule de feu test');

        $meta = $res->json('meta');
        $this->assertIsArray($meta);
        $this->assertContains('Niveau 5', $meta);
        $this->assertContains('4 PA', $meta);
    }

    public function test_kref_entity_preview_422_when_params_missing(): void
    {
        $this->getJson(route('api.cms.kref-entity-preview', ['entityType' => 'spells']))
            ->assertStatus(422);
    }
}
