<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Breed;
use App\Models\Entity\Spell;
use App\Models\Type\SpellType;
use App\Services\Seeder\Spell\ClassLevel1SpellCatalog;
use App\Services\Seeder\Spell\ClassLevel1SpellSeederImporter;
use Database\Seeders\SubEffectSeeder;
use Tests\TestCase;

final class ClassLevel1SpellSeederImporterTest extends TestCase
{
    public function test_imports_iop_level_1_kit_and_is_idempotent(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $iop = Breed::factory()->create([
            'name' => 'Iop',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Colère de Iop',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $iop->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load();
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['updated']);
        $this->assertSame([], $first['skipped']);

        $pression = Spell::query()->where('dofusdb_id', '13106')->first();
        $this->assertNotNull($pression);
        $this->assertSame('Pression', $pression->name);
        $this->assertSame(Spell::STATE_PLAYABLE, $pression->state);
        $this->assertFalse($pression->auto_update);
        $this->assertSame('3', $pression->pa);
        $this->assertSame('2', $pression->cast_per_turn);
        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $pression->resolution_mode);
        $this->assertSame('strong', $pression->attack_characteristic_key);
        $this->assertFalse($pression->is_magic);
        $this->assertSame(1, $pression->element);
        $this->assertSame(Spell::CATEGORY_CLASS, $pression->category);

        $attaque = Spell::query()->where('official_id', 'jdr:attaque-naturelle')->first();
        $this->assertNotNull($attaque);
        $this->assertSame('intel', $attaque->attack_characteristic_key);
        $this->assertTrue($attaque->po_editable);
        $this->assertSame('4', $attaque->po_max);

        $fendoir = Spell::query()->where('name', 'Fendoir')->first();
        $this->assertNotNull($fendoir);
        $this->assertSame('5', $fendoir->pa);
        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $fendoir->resolution_mode);
        $this->assertSame('sagesse', $fendoir->save_characteristic_key);
        $this->assertTrue($fendoir->is_magic);

        $bond = Spell::query()->where('dofusdb_id', '15660')->first();
        $this->assertNotNull($bond);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $bond->resolution_mode);
        $this->assertFalse($bond->sight_line);
        $this->assertSame('3', $bond->pa);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_PLAYABLE)->count());
        $this->assertGreaterThan(0, $pression->effects()->count());
        $this->assertTrue($pression->spellTypes()->where('name', 'Offensif')->exists());

        $iop->refresh();
        $pressionPivot = $iop->spells()->where('spells.id', $pression->id)->first()?->pivot;
        $this->assertNotNull($pressionPivot);
        $this->assertSame(1, (int) $pressionPivot->character_level);
        $this->assertSame(1, (int) $pressionPivot->slot_index);
        $this->assertSame(0, (int) $pressionPivot->choice_order);

        $attaquePivot = $iop->spells()->where('spells.id', $attaque->id)->first()?->pivot;
        $this->assertSame(1, (int) $attaquePivot?->slot_index);
        $this->assertSame(1, (int) $attaquePivot?->choice_order);

        $extraPivot = $iop->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertNotNull($extraPivot);
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot->character_level
        );
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_SLOT_INDEX,
            (int) $extraPivot->slot_index
        );

        $second = $importer->import($catalog);
        $this->assertSame([], $second['created']);
        $this->assertCount(6, $second['updated']);
        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_PLAYABLE)->count());
        $this->assertSame('3', $pression->fresh()->pa);
    }

    private function seedSpellTypes(): void
    {
        foreach (['Offensif', 'Buff', 'Téléportation'] as $name) {
            SpellType::factory()->create([
                'name' => $name,
                'state' => SpellType::STATE_PLAYABLE,
                'read_level' => 0,
                'write_level' => 3,
                'created_by' => null,
                'show_in_catalog' => true,
            ]);
        }
    }
}
