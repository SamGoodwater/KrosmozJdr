<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Entity\Item;
use App\Models\IaGenerationSetting;
use App\Models\User;
use App\Services\GenerativeAi\GenerationConfigLoader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IaGenerationConfigControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirects_from_ia_generation_page(): void
    {
        $this->get(route('admin.content.ia-generation.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_game_master_forbidden_from_ia_generation_page(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('admin.content.ia-generation.edit'))
            ->assertForbidden();
    }

    public function test_admin_can_view_ia_generation_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.content.ia-generation.edit'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Content/IaGeneration/Index')
                ->where('is_stored', false)
                ->has('config.entities.item')
                ->has('characteristic_options.item'));
    }

    public function test_admin_can_save_and_reset_ia_generation_settings(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $payload = $this->filePayload();
        $payload['generation']['max_retries'] = 1;
        $payload['entities']['item']['writable_characteristics'] = ['intelligence_object'];

        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->putJson(route('admin.content.ia-generation.update'), $this->formPayload($payload))
            ->assertRedirect(route('admin.content.ia-generation.edit'));

        $this->assertDatabaseCount('ia_generation_settings', 1);

        app()->forgetInstance(GenerationConfigLoader::class);
        $loader = app(GenerationConfigLoader::class);
        $this->assertSame(1, $loader->get('generation.max_retries'));
        $this->assertFalse($loader->forEntity('item')->isCharacteristicFrozen('intelligence_object'));

        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->deleteJson(route('admin.content.ia-generation.destroy'))
            ->assertRedirect(route('admin.content.ia-generation.edit'));

        $this->assertDatabaseCount('ia_generation_settings', 0);
        $this->assertSame(0, IaGenerationSetting::query()->count());
    }

    public function test_save_without_password_confirmation_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $payload = $this->filePayload();

        $this->actingAs($admin)
            ->putJson(route('admin.content.ia-generation.update'), $this->formPayload($payload))
            ->assertStatus(423);
    }

    public function test_example_ids_must_exist_and_be_playable(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Item::factory()->create([
            'name' => 'Étalon playable',
            'official_id' => 'jdr:item:etalon-test',
            'state' => Item::STATE_PLAYABLE,
        ]);
        $draft = Item::factory()->create([
            'name' => 'Brouillon',
            'state' => Item::STATE_DRAFT,
        ]);

        $payload = $this->filePayload();
        $payload['entities']['item']['example_ids'] = [$draft->id];

        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->putJson(route('admin.content.ia-generation.update'), $this->formPayload($payload))
            ->assertStatus(422)
            ->assertJsonValidationErrors('entities.item.example_ids');

        $payload['entities']['item']['example_ids'] = [999999];
        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->putJson(route('admin.content.ia-generation.update'), $this->formPayload($payload))
            ->assertStatus(422);

        $payload['entities']['item']['example_ids'] = ['jdr:item:etalon-test'];
        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->putJson(route('admin.content.ia-generation.update'), $this->formPayload($payload))
            ->assertRedirect(route('admin.content.ia-generation.edit'));

        app()->forgetInstance(GenerationConfigLoader::class);
        $this->assertSame(
            ['jdr:item:etalon-test'],
            app(GenerationConfigLoader::class)->forEntity('item')->exampleIds
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function filePayload(): array
    {
        $decoded = json_decode(
            (string) file_get_contents(base_path('resources/ia/generation.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $this->assertIsArray($decoded);

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function formPayload(array $payload): array
    {
        return [
            'generation' => $payload['generation'],
            'entities' => $payload['entities'],
        ];
    }
}
