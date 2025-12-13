<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use App\Models\Page;
use App\Enums\Visibility;
use App\Enums\SectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de validation pour StoreSectionRequest
 * 
 * Vérifie que les règles de validation sont correctement appliquées :
 * - page_id obligatoire et existant
 * - template obligatoire et valide (enum SectionType)
 * - Validation dynamique selon le template :
 *   - TEXT : data.content peut être HTML (sera sanitized)
 *   - IMAGE : data.src obligatoire, data.alt optionnel
 *   - etc.
 */
class StoreSectionRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * page_id est obligatoire (vérifié dans authorize() = 403 si absent)
     */
    public function test_page_id_required(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Test Section',
            'template' => SectionType::TEXT->value,
            // page_id manquant
        ]);

        // 403 car authorize() refuse avant la validation
        $response->assertStatus(403);
    }

    /**
     * page_id doit exister dans la table pages (vérifié dans authorize() = 403 si inexistant)
     */
    public function test_page_id_exists(): void
    {
        $admin = User::factory()->create(['role' => 4]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Test Section',
            'page_id' => 999999, // ID inexistant
            'template' => SectionType::TEXT->value,
        ]);

        // 403 car authorize() refuse avant la validation
        $response->assertStatus(403);
    }

    /**
     * template est obligatoire
     */
    public function test_template_required(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Test Section',
            'page_id' => $page->id,
            // template manquant
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('template');
    }

    /**
     * template doit être une valeur valide de l'enum SectionType
     */
    public function test_template_enum(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Test Section',
            'page_id' => $page->id,
            'template' => 'invalid-template', // Valeur invalide
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('template');
    }

    /**
     * Section TEXT : data.content peut contenir du HTML (sera sanitized côté backend)
     */
    public function test_data_validation_text_accepts_html(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Text Section',
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
            'data' => [
                'content' => '<p>Hello <strong>world</strong></p>',
            ],
        ]);

        $response->assertStatus(302); // Redirect après création
        $this->assertDatabaseHas('sections', [
            'title' => 'Text Section',
            'template' => SectionType::TEXT->value,
        ]);
    }

    /**
     * Section IMAGE : data.src est nullable (peut être rempli après création)
     */
    public function test_data_validation_image_src_nullable(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Image Section',
            'page_id' => $page->id,
            'template' => SectionType::IMAGE->value,
            'data' => [
                // src manquant = OK (nullable)
                'alt' => 'My image',
            ],
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('sections', [
            'title' => 'Image Section',
            'template' => SectionType::IMAGE->value,
        ]);
    }

    /**
     * Section IMAGE : data.alt est nullable (string sans limite spécifique dans les règles)
     */
    public function test_data_validation_image_alt_nullable(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        // Alt manquant = OK
        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Image Section',
            'page_id' => $page->id,
            'template' => SectionType::IMAGE->value,
            'data' => [
                'src' => '/storage/test.jpg',
            ],
        ]);

        $response->assertStatus(302);

        // Alt présent = OK aussi
        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Image Section 2',
            'page_id' => $page->id,
            'template' => SectionType::IMAGE->value,
            'data' => [
                'src' => '/storage/test2.jpg',
                'alt' => 'Description of image',
            ],
        ]);

        $response->assertStatus(302);
    }

    /**
     * Section GALLERY : data.images peut être un tableau vide
     */
    public function test_data_validation_gallery_images_can_be_empty(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Gallery Section',
            'page_id' => $page->id,
            'template' => SectionType::GALLERY->value,
            'data' => [
                'images' => [], // Vide = OK
            ],
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('sections', [
            'title' => 'Gallery Section',
            'template' => SectionType::GALLERY->value,
        ]);
    }

    /**
     * can_edit_role doit être une valeur valide de l'enum Visibility
     */
    public function test_can_edit_role_enum(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Test Section',
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
            'can_edit_role' => 999, // Valeur invalide
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('can_edit_role');
    }

    /**
     * Une requête valide crée la section avec succès
     */
    public function test_valid_request_creates_section(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Valid Section',
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
            'can_edit_role' => Visibility::ADMIN->value,
            'data' => [
                'content' => '<p>Test content</p>',
            ],
            'settings' => [
                'align' => 'left',
            ],
        ]);

        $response->assertStatus(302); // Redirect après création
        $this->assertDatabaseHas('sections', [
            'title' => 'Valid Section',
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
        ]);
    }
}

