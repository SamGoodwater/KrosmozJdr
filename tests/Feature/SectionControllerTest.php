<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Page;
use App\Models\Section;
use App\Enums\PageState;
use App\Enums\Visibility;
use App\Enums\SectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Tests Feature pour SectionController
 * 
 * Vérifie que :
 * - Les utilisateurs peuvent créer des sections selon les permissions
 * - Les utilisateurs peuvent modifier les sections selon les permissions
 * - Les validations fonctionnent correctement
 * - Les policies fonctionnent correctement
 * - Le réordonnancement fonctionne
 */
class SectionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Désactiver le middleware role pour les tests (on teste les policies directement)
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
        
        // Désactiver les notifications dans les tests pour éviter les erreurs
        Notification::fake();
    }

    /**
     * Test : Un utilisateur peut créer une section
     */
    public function test_user_can_create_section(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'order' => 0,
                'type' => SectionType::TEXT->value,
                'params' => [
                    'content' => 'Contenu de test',
                    'align' => 'left',
                    'size' => 'md',
                ],
                'is_visible' => Visibility::GUEST->value,
                'state' => PageState::DRAFT->value,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sections', [
            'page_id' => $page->id,
            'type' => SectionType::TEXT->value,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Test : La validation échoue si page_id est manquant
     */
    public function test_validation_fails_if_page_id_is_missing(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'order' => 0,
                'type' => SectionType::TEXT->value,
                'params' => [
                    'content' => 'Contenu de test',
                ],
                'is_visible' => Visibility::GUEST->value,
                'state' => PageState::DRAFT->value,
            ]);

        $response->assertSessionHasErrors('page_id');
    }

    /**
     * Test : La validation échoue si le type est invalide
     */
    public function test_validation_fails_if_type_is_invalid(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'order' => 0,
                'type' => 'invalid_type',
                'params' => [
                    'content' => 'Contenu de test',
                ],
                'is_visible' => Visibility::GUEST->value,
                'state' => PageState::DRAFT->value,
            ]);

        $response->assertSessionHasErrors('type');
    }

    /**
     * Test : La validation échoue si params.content est manquant pour type text
     */
    public function test_validation_fails_if_params_content_is_missing_for_text_type(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'order' => 0,
                'type' => SectionType::TEXT->value,
                'params' => [
                    'align' => 'left',
                ],
                'is_visible' => Visibility::GUEST->value,
                'state' => PageState::DRAFT->value,
            ]);

        $response->assertSessionHasErrors('params.content');
    }

    /**
     * Test : Un utilisateur peut modifier une section qu'il a créée
     */
    public function test_user_can_update_own_section(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create([
            'created_by' => $user->id,
        ]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $user->id,
            'type' => SectionType::TEXT->value,
            'params' => [
                'content' => 'Contenu original',
            ],
        ]);

        $response = $this->actingAs($user)
            ->from(route('sections.edit', $section))
            ->patch(route('sections.update', $section), [
                'page_id' => $page->id,
                'order' => $section->order,
                'type' => SectionType::TEXT->value,
                'params' => [
                    'content' => 'Contenu modifié',
                    'align' => 'center',
                    'size' => 'lg',
                ],
                'is_visible' => $section->is_visible instanceof \App\Enums\Visibility ? $section->is_visible->value : $section->is_visible,
                'state' => $section->state instanceof \App\Enums\PageState ? $section->state->value : $section->state,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sections', [
            'id' => $section->id,
        ]);
        $section->refresh();
        $this->assertEquals('Contenu modifié', $section->params['content']);
    }

    /**
     * Test : Un utilisateur peut supprimer une section qu'il a créée si il peut modifier la page
     */
    public function test_user_can_delete_own_section(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create([
            'created_by' => $user->id,
        ]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('sections.delete', $section));

        $response->assertRedirect();
        $this->assertSoftDeleted('sections', [
            'id' => $section->id,
        ]);
    }

    /**
     * Test : Un admin peut modifier n'importe quelle section
     */
    public function test_admin_can_update_any_section(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $page = Page::factory()->create();
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $otherUser->id,
            'type' => SectionType::TEXT->value,
            'params' => [
                'content' => 'Contenu original',
            ],
        ]);

        $response = $this->actingAs($admin)
            ->from(route('sections.edit', $section))
            ->patch(route('sections.update', $section), [
                'page_id' => $page->id,
                'order' => $section->order,
                'type' => SectionType::TEXT->value,
                'params' => [
                    'content' => 'Contenu modifié par admin',
                ],
                'is_visible' => $section->is_visible instanceof \App\Enums\Visibility ? $section->is_visible->value : $section->is_visible,
                'state' => $section->state instanceof \App\Enums\PageState ? $section->state->value : $section->state,
            ]);

        $response->assertRedirect();
        $section->refresh();
        $this->assertEquals('Contenu modifié par admin', $section->params['content']);
    }

    /**
     * Test : Le réordonnancement des sections fonctionne
     */
    public function test_reorder_sections_works(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create();
        $section1 = Section::factory()->create([
            'page_id' => $page->id,
            'order' => 0,
        ]);
        $section2 = Section::factory()->create([
            'page_id' => $page->id,
            'order' => 1,
        ]);
        $section3 = Section::factory()->create([
            'page_id' => $page->id,
            'order' => 2,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('sections.reorder'), [
                'sections' => [
                    ['id' => $section3->id, 'order' => 0],
                    ['id' => $section1->id, 'order' => 1],
                    ['id' => $section2->id, 'order' => 2],
                ],
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertEquals(0, $section3->fresh()->order);
        $this->assertEquals(1, $section1->fresh()->order);
        $this->assertEquals(2, $section2->fresh()->order);
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas créer une section
     */
    public function test_guest_cannot_create_section(): void
    {
        $page = Page::factory()->create();

        $response = $this->post(route('sections.store'), [
            'page_id' => $page->id,
            'order' => 0,
            'type' => SectionType::TEXT->value,
            'params' => [
                'content' => 'Contenu de test',
            ],
            'is_visible' => Visibility::GUEST->value,
            'state' => PageState::DRAFT->value,
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : La page index charge les sections
     */
    public function test_index_page_loads_sections(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();
        Section::factory()->count(5)->create(['page_id' => $page->id]);

        $response = $this->actingAs($user)
            ->get(route('sections.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/section/Index')
            ->has('sections.data', 5)
        );
    }

    /**
     * Test : Un utilisateur peut voir une section publiée avec visibilité guest
     */
    public function test_user_can_view_published_section_with_guest_visibility(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'state' => PageState::PUBLISHED->value,
            'is_visible' => Visibility::GUEST->value,
        ]);

        $response = $this->actingAs($user)
            ->get(route('sections.show', $section));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/section/Show')
            ->has('section')
        );
    }
}

