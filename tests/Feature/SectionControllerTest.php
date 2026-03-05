<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Page;
use App\Models\Section;
use App\Enums\SectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
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
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'state' => Section::STATE_DRAFT,
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
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'state' => Section::STATE_DRAFT,
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
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'state' => Section::STATE_DRAFT,
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
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'state' => Section::STATE_DRAFT,
            ]);

        $response->assertSessionHasErrors('params.content');
    }

    /**
     * Test : Validation template entity_table (entity invalide).
     */
    public function test_validation_fails_if_entity_table_entity_is_invalid(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::ENTITY_TABLE->value,
                'settings' => [
                    'entity' => 'unknown_entity',
                    'filters' => [],
                    'limit' => 50,
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertSessionHasErrors('settings.entity');
    }

    /**
     * Test : Validation template entity_table (limit hors bornes).
     */
    public function test_validation_fails_if_entity_table_limit_is_out_of_range(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::ENTITY_TABLE->value,
                'settings' => [
                    'entity' => 'spells',
                    'filters' => [],
                    'limit' => 900,
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertSessionHasErrors('settings.limit');
    }

    /**
     * Test : Création entity_table valide avec payload settings-only.
     */
    public function test_user_can_create_entity_table_section_with_settings_only_payload(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::ENTITY_TABLE->value,
                'settings' => [
                    'entity' => 'spells',
                    'filters' => ['state' => 'playable'],
                    'limit' => 120,
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sections', [
            'page_id' => $page->id,
            'template' => SectionType::ENTITY_TABLE->value,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Test : Validation image refuse les protocoles non sûrs.
     */
    public function test_validation_fails_if_image_source_uses_unsafe_protocol(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::IMAGE->value,
                'data' => [
                    'src' => 'javascript:alert(1)',
                    'alt' => 'Image test',
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertSessionHasErrors('data.src');
    }

    /**
     * Test : Validation vidéo youtube refuse une URL non-youtube.
     */
    public function test_validation_fails_if_youtube_video_source_is_not_youtube(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::VIDEO->value,
                'data' => [
                    'type' => 'youtube',
                    'src' => 'https://example.com/video.mp4',
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertSessionHasErrors('data.src');
    }

    /**
     * Test : Validation vidéo directe refuse les extensions non supportées.
     */
    public function test_validation_fails_if_direct_video_extension_is_not_supported(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::VIDEO->value,
                'data' => [
                    'type' => 'direct',
                    'src' => 'https://cdn.example.com/video.txt',
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertSessionHasErrors('data.src');
    }

    /**
     * Test : Validation vidéo youtube accepte un ID valide.
     */
    public function test_user_can_create_video_section_with_valid_youtube_id(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::VIDEO->value,
                'data' => [
                    'type' => 'youtube',
                    'src' => 'dQw4w9WgXcQ',
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sections', [
            'page_id' => $page->id,
            'template' => SectionType::VIDEO->value,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Test : Création vidéo youtube avec URL complète (persistée telle quelle).
     */
    public function test_user_can_create_video_section_with_valid_youtube_url(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);
        $youtubeUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::VIDEO->value,
                'data' => [
                    'type' => 'youtube',
                    'src' => $youtubeUrl,
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect();

        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('template', SectionType::VIDEO->value)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('youtube', $section->data['type'] ?? null);
        $this->assertSame($youtubeUrl, $section->data['src'] ?? null);
    }

    /**
     * Test : Création vidéo vimeo avec URL complète (persistée telle quelle).
     */
    public function test_user_can_create_video_section_with_valid_vimeo_url(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);
        $vimeoUrl = 'https://vimeo.com/123456789';

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::VIDEO->value,
                'data' => [
                    'type' => 'vimeo',
                    'src' => $vimeoUrl,
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect();

        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('template', SectionType::VIDEO->value)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('vimeo', $section->data['type'] ?? null);
        $this->assertSame($vimeoUrl, $section->data['src'] ?? null);
    }

    /**
     * Test : Validation vidéo refuse un mode d'affichage direct invalide.
     */
    public function test_validation_fails_if_video_direct_display_mode_is_invalid(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::VIDEO->value,
                'settings' => [
                    'directVideoDisplayMode' => 'invalid_mode',
                ],
                'data' => [
                    'type' => 'direct',
                    'src' => 'https://cdn.example.com/video.mp4',
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertSessionHasErrors('settings.directVideoDisplayMode');
    }

    /**
     * Test : Création vidéo directe avec mode téléchargement uniquement.
     */
    public function test_user_can_create_direct_video_section_with_download_only_mode(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::VIDEO->value,
                'settings' => [
                    'directVideoDisplayMode' => 'download',
                ],
                'data' => [
                    'type' => 'direct',
                    'src' => 'https://cdn.example.com/video.mp4',
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect();
        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('template', SectionType::VIDEO->value)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('download', $section->settings['directVideoDisplayMode'] ?? null);
        $this->assertSame('direct', $section->data['type'] ?? null);
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
                'read_level' => $section->read_level,
                'write_level' => $section->write_level,
                'state' => $section->state,
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
        $page = Page::factory()->create([
            'write_level' => User::ROLE_ADMIN,
        ]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $otherUser->id,
            'type' => SectionType::TEXT->value,
            'params' => [
                'content' => 'Contenu original',
            ],
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
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
                'read_level' => $section->read_level,
                'write_level' => $section->write_level,
                'state' => $section->state,
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
        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'write_level' => User::ROLE_ADMIN,
        ]);
        $section1 = Section::factory()->create([
            'page_id' => $page->id,
            'order' => 0,
            'created_by' => $admin->id,
            'write_level' => User::ROLE_ADMIN,
        ]);
        $section2 = Section::factory()->create([
            'page_id' => $page->id,
            'order' => 1,
            'created_by' => $admin->id,
            'write_level' => User::ROLE_ADMIN,
        ]);
        $section3 = Section::factory()->create([
            'page_id' => $page->id,
            'order' => 2,
            'created_by' => $admin->id,
            'write_level' => User::ROLE_ADMIN,
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
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'state' => Section::STATE_DRAFT,
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
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);

        $response = $this->actingAs($user)
            ->get(route('sections.show', $section));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/section/Show')
            ->has('section')
        );
    }

    /**
     * Test : Upload d'un fichier sur une section (Media Library).
     */
    public function test_section_file_upload_via_media_library(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $user->id,
            'type' => SectionType::TEXT->value,
            'write_level' => User::ROLE_GUEST,
        ]);
        $page->update(['write_level' => User::ROLE_GUEST]);

        $file = UploadedFile::fake()->image('section-file.jpg', 100, 100);

        $response = $this->actingAs($user)
            ->post(route('sections.files.store', $section), [
                'file' => $file,
                'title' => 'Titre fichier',
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('file.id', fn ($id) => is_numeric($id));
        $response->assertJsonPath('file.url', fn ($url) => is_string($url) && $url !== '');
        $this->assertCount(1, $section->fresh()->getMedia('files'));
    }

    /**
     * Test : Upload d'une archive zip autorisée sur une section.
     */
    public function test_section_zip_file_upload_via_media_library(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $user->id,
            'type' => SectionType::IMAGE->value,
            'write_level' => User::ROLE_GUEST,
        ]);
        $page->update(['write_level' => User::ROLE_GUEST]);

        $file = UploadedFile::fake()->create('documents.zip', 120, 'application/zip');

        $response = $this->actingAs($user)
            ->post(route('sections.files.store', $section), [
                'file' => $file,
                'title' => 'Archive test',
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('file.url', fn ($url) => is_string($url) && str_contains($url, '.zip'));
        $this->assertCount(1, $section->fresh()->getMedia('files'));
    }

    /**
     * Test : Validation média accepte une URL document (docx).
     */
    public function test_user_can_create_image_template_with_document_url_source(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::IMAGE->value,
                'data' => [
                    'src' => 'https://cdn.example.com/guide.docx',
                    'alt' => 'Guide',
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sections', [
            'page_id' => $page->id,
            'template' => SectionType::IMAGE->value,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Test : Validation image refuse un mode d'affichage document invalide.
     */
    public function test_validation_fails_if_image_document_display_mode_is_invalid(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::IMAGE->value,
                'settings' => [
                    'documentDisplayMode' => 'invalid_mode',
                ],
                'data' => [
                    'src' => 'https://cdn.example.com/guide.pdf',
                    'alt' => 'Guide',
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertSessionHasErrors('settings.documentDisplayMode');
    }

    /**
     * Test : Création image avec mode téléchargement uniquement.
     */
    public function test_user_can_create_image_template_with_download_only_document_mode(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('sections.store'), [
                'page_id' => $page->id,
                'template' => SectionType::IMAGE->value,
                'settings' => [
                    'documentDisplayMode' => 'download',
                ],
                'data' => [
                    'src' => 'https://cdn.example.com/guide.pdf',
                    'alt' => 'Guide',
                ],
                'state' => Section::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect();
        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('template', SectionType::IMAGE->value)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('download', $section->settings['documentDisplayMode'] ?? null);
    }

    /**
     * Test : Suppression d'un fichier (média) d'une section.
     */
    public function test_section_file_delete(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $user->id,
        ]);
        $section->addMedia(UploadedFile::fake()->image('doc.jpg'))->toMediaCollection('files');
        $media = $section->getMedia('files')->first();

        $response = $this->actingAs($user)
            ->delete(route('sections.files.delete', [$section, $media]));

        $response->assertOk();
        $this->assertCount(0, $section->fresh()->getMedia('files'));
    }
}

