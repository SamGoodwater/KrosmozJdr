<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Breed;
use App\Models\Entity\Capability;
use App\Services\Seeder\Capability\ClassPassiveCatalog;
use App\Services\Seeder\Capability\ClassPassiveSeederImporter;
use Database\Seeders\Entity\ClassBreedSeeder;
use Tests\TestCase;

final class ClassPassiveSeederImporterTest extends TestCase
{
    public function test_imports_nineteen_class_passives_and_is_idempotent(): void
    {
        $this->seed(ClassBreedSeeder::class);

        $importer = app(ClassPassiveSeederImporter::class);
        $first = $importer->import();

        $this->assertCount(19, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);

        $iop = Breed::query()->where('name', 'Iop')->first();
        $this->assertNotNull($iop);
        $fureur = $iop->capabilities()->where('name', 'Fureur')->first();
        $this->assertNotNull($fureur);
        $this->assertTrue($fureur->is_passive);
        $this->assertSame(Capability::STATE_PLAYABLE, $fureur->state);
        $this->assertSame('0', $fureur->pa);
        $this->assertSame('etat', $fureur->powerful);
        $this->assertSame(1, $iop->capabilities()->count());

        $hupper = Breed::query()->where('name', 'Huppermage')->first();
        $this->assertNotNull($hupper);
        $contraste = $hupper->capabilities()->where('name', 'Contraste')->first();
        $this->assertNotNull($contraste);
        $this->assertStringContainsString('+1 dégât', (string) $contraste->effect);
        $this->assertStringContainsString('pas un dé', (string) $contraste->effect);

        $second = $importer->import();
        $this->assertSame([], $second['created']);
        $this->assertCount(19, $second['updated']);
        $this->assertSame(19, Capability::query()->where('is_passive', true)->whereIn(
            'name',
            array_column(ClassPassiveCatalog::load()->entries(), 'name')
        )->count());
    }

    public function test_does_not_change_state_or_detach_other_passives(): void
    {
        $this->seed(ClassBreedSeeder::class);
        $iop = Breed::query()->where('name', 'Iop')->first();
        $this->assertNotNull($iop);

        $extraPassive = Capability::factory()->create([
            'name' => 'Discipline de fer',
            'is_passive' => true,
            'state' => Capability::STATE_DRAFT,
            'created_by' => null,
        ]);
        $extraActive = Capability::factory()->create([
            'name' => 'Fendoir de classe',
            'is_passive' => false,
            'state' => Capability::STATE_DRAFT,
            'created_by' => null,
        ]);
        $iop->capabilities()->attach([$extraPassive->id, $extraActive->id]);

        app(ClassPassiveSeederImporter::class)->import();

        $fureur = Capability::query()->where('name', 'Fureur')->first();
        $this->assertNotNull($fureur);
        $fureur->state = Capability::STATE_DRAFT;
        $fureur->save();

        app(ClassPassiveSeederImporter::class)->import();
        $fureur->refresh();
        $this->assertSame(Capability::STATE_DRAFT, $fureur->state);
        $this->assertTrue($iop->capabilities()->where('capabilities.id', $extraPassive->id)->exists());
        $this->assertFalse($iop->capabilities()->where('capabilities.id', $extraActive->id)->exists());
        $this->assertTrue($iop->capabilities()->where('name', 'Fureur')->exists());
        $this->assertSame(2, $iop->capabilities()->count());
    }
}
