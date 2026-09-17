<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
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

    public function test_admin_without_password_is_redirected_from_ia_generation_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.content.ia-generation.edit'))
            ->assertRedirect(route('password.confirm'));
    }

    public function test_admin_can_view_ia_generation_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAsConfirmed($admin)
            ->get(route('admin.content.ia-generation.edit'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Content/IaGeneration/Index')
                ->where('is_stored', false)
                ->has('config.entities.item')
                ->has('config.entities.consumable')
                ->has('config.supervisor_prompt')
                ->has('characteristic_options.item')
                ->has('usage')
                ->has('usage.local_input_tokens')
                ->has('estimates')
                ->has('available_models'));
    }

    public function test_admin_can_save_and_reset_ia_generation_settings(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $payload = $this->filePayload();
        $payload['generation']['max_retries'] = 1;
        $payload['generation']['model'] = 'claude-sonnet-5';
        $payload['generation']['prompt_cache'] = false;
        $payload['entities']['item']['writable_characteristics'] = ['intelligence_object'];
        $payload['supervisor_prompt'] = 'Prompt superviseur de test.';
        foreach (array_keys($payload['entities']) as $type) {
            $payload['entities'][$type]['example_ids'] = [];
        }

        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->putJson(route('admin.content.ia-generation.update'), $this->formPayload($payload))
            ->assertRedirect(route('admin.content.ia-generation.edit'));

        $this->assertDatabaseCount('ia_generation_settings', 1);

        app()->forgetInstance(GenerationConfigLoader::class);
        $loader = app(GenerationConfigLoader::class);
        $this->assertSame(1, $loader->get('generation.max_retries'));
        $this->assertSame('claude-sonnet-5', $loader->get('generation.model'));
        $this->assertFalse($loader->get('generation.prompt_cache'));
        $this->assertFalse($loader->forEntity('item')->isCharacteristicFrozen('intelligence_object'));
        $this->assertSame('Prompt superviseur de test.', $loader->get('supervisor_prompt'));

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
        foreach (array_keys($payload['entities']) as $type) {
            $payload['entities'][$type]['example_ids'] = [];
        }
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

    public function test_admin_can_save_playable_few_shot_panoplies(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Panoply::factory()->create([
            'name' => 'Panoplie du Bouftou',
            'state' => Panoply::STATE_PLAYABLE,
        ]);

        $payload = $this->filePayload();
        foreach (array_keys($payload['entities']) as $type) {
            $payload['entities'][$type]['example_ids'] = [];
        }
        $payload['entities']['item']['few_shot_panoplies'] = ['Panoplie du Bouftou'];

        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->putJson(route('admin.content.ia-generation.update'), $this->formPayload($payload))
            ->assertRedirect(route('admin.content.ia-generation.edit'));

        app()->forgetInstance(GenerationConfigLoader::class);
        $this->assertSame(
            ['Panoplie du Bouftou'],
            app(GenerationConfigLoader::class)->forEntity('item')->extra['few_shot_panoplies'] ?? null
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
            'supervisor_prompt' => $payload['supervisor_prompt'] ?? '',
            'generation' => $payload['generation'],
            'entities' => $payload['entities'],
        ];
    }
}
