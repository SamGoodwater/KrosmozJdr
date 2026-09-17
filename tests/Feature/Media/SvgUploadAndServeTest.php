<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Enums\SectionType;
use App\Http\Middleware\CheckRole;
use App\Models\Characteristic;
use App\Models\Entity\Resource;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SvgUploadAndServeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
        Storage::fake('public');
    }

    public function test_svg_upload_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $svg = UploadedFile::fake()->create('xss.svg', 12, 'image/svg+xml');

        $this->actingAs($admin)
            ->postJson(route('api.entities.resources.upload-image'), [
                'file' => $svg,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    public function test_rejected_resource_reupload_keeps_existing_image(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $resource = Resource::factory()->create([
            'created_by' => $admin->id,
            'write_level' => User::ROLE_ADMIN,
        ]);
        $original = $resource->addMedia(UploadedFile::fake()->image('wheat.png', 32, 32))
            ->toMediaCollection('images');
        $resource->update(['image' => $original->getUrl()]);
        $originalUrl = $resource->image;
        $originalId = $original->id;

        $this->actingAs($admin)
            ->postJson(route('api.entities.resources.upload-image'), [
                'resource_id' => $resource->id,
                'file' => UploadedFile::fake()->create('xss.svg', 12, 'image/svg+xml'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');

        $resource->refresh();
        $this->assertSame($originalUrl, $resource->image);
        $this->assertCount(1, $resource->getMedia('images'));
        $this->assertSame($originalId, $resource->getFirstMedia('images')?->id);
    }

    public function test_missing_file_on_resource_reupload_keeps_existing_image(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $resource = Resource::factory()->create([
            'created_by' => $admin->id,
            'write_level' => User::ROLE_ADMIN,
        ]);
        $original = $resource->addMedia(UploadedFile::fake()->image('wheat.png', 32, 32))
            ->toMediaCollection('images');
        $resource->update(['image' => $original->getUrl()]);
        $originalId = $original->id;

        $this->actingAs($admin)
            ->postJson(route('api.entities.resources.upload-image'), [
                'resource_id' => $resource->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');

        $resource->refresh();
        $this->assertCount(1, $resource->getMedia('images'));
        $this->assertSame($originalId, $resource->getFirstMedia('images')?->id);
    }

    public function test_successful_resource_reupload_replaces_existing_image(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $resource = Resource::factory()->create([
            'created_by' => $admin->id,
            'write_level' => User::ROLE_ADMIN,
        ]);
        $original = $resource->addMedia(UploadedFile::fake()->image('old.png', 32, 32))
            ->toMediaCollection('images');
        $resource->update(['image' => $original->getUrl()]);
        $originalId = $original->id;

        $this->actingAs($admin)
            ->postJson(route('api.entities.resources.upload-image'), [
                'resource_id' => $resource->id,
                'file' => UploadedFile::fake()->image('new.png', 32, 32),
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $resource->refresh();
        $this->assertCount(1, $resource->getMedia('images'));
        $this->assertNotSame($originalId, $resource->getFirstMedia('images')?->id);
        $this->assertNotEmpty($resource->image);
    }

    public function test_rejected_characteristic_icon_reupload_keeps_existing_icon(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $characteristic = Characteristic::create([
            'key' => 'test_icon_preserve',
            'name' => 'Icône test',
            'type' => 'int',
            'sort_order' => 0,
        ]);
        $original = $characteristic->addMedia(UploadedFile::fake()->image('icon.png', 32, 32))
            ->toMediaCollection('icons');
        $characteristic->update(['icon' => $original->getUrl()]);
        $originalId = $original->id;

        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->postJson(route('admin.characteristics.upload-icon'), [
                'characteristic_id' => $characteristic->id,
                'file' => UploadedFile::fake()->create('xss.svg', 12, 'image/svg+xml'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');

        $characteristic->refresh();
        $this->assertCount(1, $characteristic->getMedia('icons'));
        $this->assertSame($originalId, $characteristic->getFirstMedia('icons')?->id);
        $this->assertNotEmpty($characteristic->icon);
    }

    public function test_rejected_avatar_reupload_keeps_existing_avatar(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $original = $user->addMedia(UploadedFile::fake()->image('avatar.png', 64, 64))
            ->toMediaCollection('avatars');
        $user->update(['avatar' => $original->getUrl()]);
        $originalId = $original->id;

        $this->actingAs($user)
            ->post(route('user.updateAvatar'), [
                'avatar' => UploadedFile::fake()->create('xss.svg', 12, 'image/svg+xml'),
            ])
            ->assertSessionHasErrors('avatar');

        $user->refresh();
        $this->assertCount(1, $user->getMedia('avatars'));
        $this->assertSame($originalId, $user->getFirstMedia('avatars')?->id);
    }

    public function test_section_svg_upload_is_rejected(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $user->id, 'write_level' => User::ROLE_GUEST]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $user->id,
            'type' => SectionType::IMAGE->value,
            'write_level' => User::ROLE_GUEST,
        ]);

        $this->actingAs($user)
            ->postJson(route('sections.files.store', $section), [
                'file' => UploadedFile::fake()->create('xss.svg', 12, 'image/svg+xml'),
                'title' => 'SVG malicieux',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('file');

        $this->assertCount(0, $section->fresh()->getMedia('files'));
    }

    public function test_svg_is_not_served_as_a_document(): void
    {
        $path = 'images/evil.svg';
        Storage::disk('public')->put($path, '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"></svg>');

        $this->get(route('media.show', ['path' => $path]))
            ->assertStatus(415)
            ->assertJsonFragment(['error' => 'Les SVG ne sont pas servis comme document (risque XSS).']);
    }
}
