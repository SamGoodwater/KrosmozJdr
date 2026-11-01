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
    public function test_imports_passives_and_class_capabilities_and_is_idempotent(): void
    {
        $this->seed(ClassBreedSeeder::class);

        $importer = app(ClassPassiveSeederImporter::class);
        $first = $importer->import();

        $this->assertCount(28, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);

        $iop = Breed::query()->where('name', 'Iop')->first();
        $this->assertNotNull($iop);
        $passive = $iop->capabilities()->where('name', 'Tête brûlée')->first();
        $this->assertNotNull($passive);
        $this->assertTrue($passive->is_passive);
        $this->assertSame(Capability::STATE_PLAYABLE, $passive->state);
        $this->assertSame('0', $passive->pa);

        $fureur = $iop->capabilities()->where('name', 'Fureur')->first();
        $this->assertNotNull($fureur);
        $this->assertFalse($fureur->is_passive);
        $this->assertSame('3', $fureur->level);
        $this->assertSame(
            ['3', '9', '14'],
            $iop->capabilities()->where('is_passive', false)->orderByRaw('CAST(level AS UNSIGNED)')->pluck('level')->all()
        );
        $this->assertSame(4, $iop->capabilities()->count());

        $hupper = Breed::query()->where('name', 'Huppermage')->first();
        $this->assertNotNull($hupper);
        $contraste = $hupper->capabilities()->where('name', 'Contraste')->first();
        $this->assertNotNull($contraste);
        $this->assertStringContainsString('+1 dégât', (string) $contraste->effect);
        $this->assertSame(1, $hupper->capabilities()->count());

        $second = $importer->import();
        $this->assertSame([], $second['created']);
        $this->assertCount(28, $second['updated']);
        $this->assertSame(19, Capability::query()->where('is_passive', true)->whereIn(
            'name',
            array_column(ClassPassiveCatalog::load()->entries(), 'name')
        )->count());
        $this->assertSame(4, $iop->capabilities()->count());
    }

    public function test_converts_former_passive_into_class_capability(): void
    {
        $this->seed(ClassBreedSeeder::class);
        $ecaflip = Breed::query()->where('name', 'Ecaflip')->first();
        $this->assertNotNull($ecaflip);

        $formerPassive = Capability::factory()->create([
            'name' => 'La pièce',
            'is_passive' => true,
            'state' => Capability::STATE_PLAYABLE,
            'created_by' => null,
        ]);
        $ecaflip->capabilities()->attach($formerPassive->id);

        app(ClassPassiveSeederImporter::class)->import();

        $formerPassive->refresh();
        $this->assertSame('Pièce truquée', $formerPassive->name);
        $this->assertFalse($formerPassive->is_passive);
        $this->assertSame('3', $formerPassive->level);
        $this->assertTrue($ecaflip->capabilities()->where('capabilities.id', $formerPassive->id)->exists());
        $this->assertTrue($ecaflip->capabilities()->where('name', 'Retomber sur ses pattes')->where('is_passive', true)->exists());
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

        $passive = Capability::query()->where('name', 'Tête brûlée')->first();
        $this->assertNotNull($passive);
        $passive->state = Capability::STATE_DRAFT;
        $passive->save();

        app(ClassPassiveSeederImporter::class)->import();
        $passive->refresh();
        $this->assertSame(Capability::STATE_DRAFT, $passive->state);
        $this->assertTrue($iop->capabilities()->where('capabilities.id', $extraPassive->id)->exists());
        $this->assertFalse($iop->capabilities()->where('capabilities.id', $extraActive->id)->exists());
        $this->assertTrue($iop->capabilities()->where('name', 'Fureur')->exists());
        $this->assertSame(5, $iop->capabilities()->count());
    }
}
