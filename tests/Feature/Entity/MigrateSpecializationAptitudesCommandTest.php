<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Aptitude;
use App\Models\Entity\Breed;
use App\Models\Entity\Capability;
use App\Models\Entity\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MigrateSpecializationAptitudesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrates_passive_spe_capabilities_and_skips_breed_passives(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $spe = Specialization::factory()->create(['created_by' => $admin->id]);
        $breed = Breed::factory()->create(['created_by' => $admin->id]);

        $spePassive = Capability::factory()->create([
            'name' => 'Aptitude à migrer',
            'is_passive' => true,
            'description' => 'Aptitude de spécialisation',
            'effect' => 'Bonus',
            'state' => Capability::STATE_PLAYABLE,
            'created_by' => $admin->id,
        ]);
        $spe->capabilities()->attach($spePassive->id, ['level' => 3]);

        $classPassive = Capability::factory()->create([
            'name' => 'Passif de classe',
            'is_passive' => true,
            'state' => Capability::STATE_PLAYABLE,
            'created_by' => $admin->id,
        ]);
        $breed->capabilities()->attach($classPassive->id);

        Artisan::call('entities:migrate-specialization-aptitudes');

        $this->assertDatabaseHas('aptitudes', ['name' => 'Aptitude à migrer']);
        $aptitude = Aptitude::query()->where('name', 'Aptitude à migrer')->first();
        $this->assertNotNull($aptitude);
        $this->assertTrue($spe->fresh()->aptitudes()->where('aptitudes.id', $aptitude->id)->exists());
        $this->assertSame(3, (int) $spe->fresh()->aptitudes()->where('aptitudes.id', $aptitude->id)->first()?->pivot->level);
        $this->assertFalse($spe->fresh()->capabilities()->where('capabilities.id', $spePassive->id)->exists());
        $this->assertSoftDeleted('capabilities', ['id' => $spePassive->id]);

        $this->assertDatabaseHas('capabilities', ['id' => $classPassive->id, 'deleted_at' => null]);
        $this->assertTrue($breed->fresh()->capabilities()->where('capabilities.id', $classPassive->id)->exists());
    }
}
