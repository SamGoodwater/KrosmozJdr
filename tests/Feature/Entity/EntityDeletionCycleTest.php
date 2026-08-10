<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

/**
 * Cycle soft delete → restore → force delete via l’API générique.
 *
 * @example php artisan test --filter=EntityDeletionCycleTest
 */
class EntityDeletionCycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_spell_soft_delete_restore_then_force_delete(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'name' => 'Sort cycle suppression',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->deleteJson("/api/entities/spells/{$spell->id}")
            ->assertOk();

        $this->assertSoftDeleted('spells', ['id' => $spell->id]);

        $this->actingAs($admin)
            ->postJson("/api/entities/spells/{$spell->id}/restore")
            ->assertOk();

        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
            'deleted_at' => null,
        ]);

        $this->actingAs($admin)
            ->deleteJson("/api/entities/spells/{$spell->id}")
            ->assertOk();

        $this->actingAs($admin)
            ->deleteJson("/api/entities/spells/{$spell->id}/force")
            ->assertOk();

        $this->assertDatabaseMissing('spells', ['id' => $spell->id]);
    }

    public function test_force_delete_purges_spatie_media_and_disk_file(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'name' => 'Sort avec image',
            'created_by' => $admin->id,
        ]);

        $media = $spell
            ->addMedia(UploadedFile::fake()->image('spell.png', 32, 32))
            ->toMediaCollection('images');

        $relativePath = $media->getPathRelativeToRoot();
        $this->assertTrue(Storage::disk('public')->exists($relativePath));
        $this->assertDatabaseHas('media', ['id' => $media->id]);

        $spell->delete();
        app(EntityDeletionService::class)->forceDelete($spell->fresh(), $admin);

        $this->assertDatabaseMissing('spells', ['id' => $spell->id]);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        $this->assertFalse(Storage::disk('public')->exists($relativePath));
        $this->assertSame(0, Media::query()->where('model_type', Spell::class)->where('model_id', $spell->id)->count());
    }

    public function test_force_delete_without_trash_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'name' => 'Sort non corbeille',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->deleteJson("/api/entities/spells/{$spell->id}/force")
            ->assertStatus(422);

        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
            'deleted_at' => null,
        ]);
    }

    public function test_service_force_delete_guard_message(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'name' => 'Sort garde force',
            'created_by' => $admin->id,
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(EntityDeletionService::class)->forceDelete($spell, $admin);
    }
}
