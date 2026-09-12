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

    public function test_imports_cra_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $cra = Breed::factory()->create([
            'name' => 'Crâ',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Flèche Punitive',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $cra->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::craPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $cinglante = Spell::query()->where('dofusdb_id', '32427')->first();
        $this->assertNotNull($cinglante);
        $this->assertSame('Flèche Cinglante', $cinglante->name);
        $this->assertSame(Spell::STATE_PLAYABLE, $cinglante->state);
        $this->assertFalse($cinglante->auto_update);
        $this->assertSame('3', $cinglante->pa);
        $this->assertSame('2', $cinglante->po_min);
        $this->assertSame('6', $cinglante->po_max);
        $this->assertTrue($cinglante->po_editable);
        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $cinglante->resolution_mode);
        $this->assertSame('agi', $cinglante->attack_characteristic_key);
        $this->assertSame(3, $cinglante->element);
        $this->assertSame(Spell::CATEGORY_CLASS, $cinglante->category);

        $glacee = Spell::query()->where('dofusdb_id', '32435')->first();
        $this->assertSame('chance', $glacee?->attack_characteristic_key);
        $this->assertSame(4, $glacee?->element);

        $explosive = Spell::query()->where('dofusdb_id', '32445')->first();
        $this->assertSame('5', $explosive?->pa);
        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $explosive?->resolution_mode);
        $this->assertSame('sagesse', $explosive?->save_characteristic_key);
        $this->assertTrue($explosive?->is_magic);

        $lynx = Spell::query()->where('official_id', 'jdr:oeil-de-lynx')->first();
        $this->assertNotNull($lynx);
        $this->assertSame('Œil de Lynx', $lynx->name);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $lynx->resolution_mode);
        $this->assertSame('3', $lynx->pa);

        $recul = Spell::query()->where('dofusdb_id', '13055')->first();
        $this->assertSame('1', $recul?->po_min);
        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $recul?->resolution_mode);
        $this->assertFalse($recul?->is_magic);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_PLAYABLE)->count());

        $cra->refresh();
        $cinglantePivot = $cra->spells()->where('spells.id', $cinglante->id)->first()?->pivot;
        $this->assertSame(1, (int) $cinglantePivot?->character_level);
        $this->assertSame(1, (int) $cinglantePivot?->slot_index);
        $this->assertSame(0, (int) $cinglantePivot?->choice_order);

        $lynxPivot = $cra->spells()->where('spells.id', $lynx->id)->first()?->pivot;
        $this->assertSame(3, (int) $lynxPivot?->slot_index);
        $this->assertSame(0, (int) $lynxPivot?->choice_order);

        $extraPivot = $cra->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_SLOT_INDEX,
            (int) $extraPivot?->slot_index
        );
    }

    public function test_imports_eniripsa_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $eni = Breed::factory()->create([
            'name' => 'Eniripsa',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Mot de Reconstitution',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $eni->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::eniripsaPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $vivifiant = Spell::query()->where('dofusdb_id', '28572')->first();
        $this->assertNotNull($vivifiant);
        $this->assertSame('Mot Vivifiant', $vivifiant->name);
        $this->assertSame(Spell::STATE_PLAYABLE, $vivifiant->state);
        $this->assertFalse($vivifiant->auto_update);
        $this->assertSame('3', $vivifiant->pa);
        $this->assertSame('2', $vivifiant->cast_per_turn);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $vivifiant->resolution_mode);
        $this->assertTrue($vivifiant->auto_success_if_willing_target);
        $this->assertSame(4, $vivifiant->element);
        $this->assertTrue($vivifiant->spellTypes()->where('name', 'Soin')->exists());

        $interdit = Spell::query()->where('dofusdb_id', '25873')->first();
        $this->assertSame('5', $interdit?->pa);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $interdit?->resolution_mode);

        $frayeur = Spell::query()->where('dofusdb_id', '13175')->first();
        $this->assertSame(Spell::RESOLUTION_ATTACK_ROLL, $frayeur?->resolution_mode);
        $this->assertSame('agi', $frayeur?->attack_characteristic_key);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_PLAYABLE)->count());

        $eni->refresh();
        $vivifiantPivot = $eni->spells()->where('spells.id', $vivifiant->id)->first()?->pivot;
        $this->assertSame(1, (int) $vivifiantPivot?->character_level);
        $this->assertSame(1, (int) $vivifiantPivot?->slot_index);
        $this->assertSame(0, (int) $vivifiantPivot?->choice_order);

        $extraPivot = $eni->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_SLOT_INDEX,
            (int) $extraPivot?->slot_index
        );
    }

    public function test_imports_sram_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $sram = Breed::factory()->create([
            'name' => 'Sram',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Marque Mortuaire',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $sram->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sramPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $sournoiserie = Spell::query()->where('dofusdb_id', '32536')->first();
        $this->assertNotNull($sournoiserie);
        $this->assertSame('Sournoiserie', $sournoiserie->name);
        $this->assertSame('3', $sournoiserie->pa);
        $this->assertSame('strong', $sournoiserie->attack_characteristic_key);
        $this->assertSame(1, $sournoiserie->element);

        $piege = Spell::query()->where('dofusdb_id', '12929')->first();
        $this->assertSame('5', $piege?->pa);
        $this->assertSame('trap', $piege?->target_type);
        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $piege?->resolution_mode);
        $this->assertSame('sagesse', $piege?->save_characteristic_key);

        $invis = Spell::query()->where('dofusdb_id', '32367')->first();
        $this->assertSame('5', $invis?->pa);
        $this->assertSame(Spell::RESOLUTION_AUTO_SUCCESS, $invis?->resolution_mode);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_PLAYABLE)->count());

        $sram->refresh();
        $piegePivot = $sram->spells()->where('spells.id', $piege->id)->first()?->pivot;
        $this->assertSame(2, (int) $piegePivot?->slot_index);
        $this->assertSame(0, (int) $piegePivot?->choice_order);

        $extraPivot = $sram->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
    }

    public function test_imports_xelor_level_1_kit_and_parks_extra_spells(): void
    {
        $this->seed(SubEffectSeeder::class);
        $this->seedSpellTypes();

        $xelor = Breed::factory()->create([
            'name' => 'Xélor',
            'state' => Breed::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $extra = Spell::factory()->create([
            'name' => 'Momification',
            'state' => Spell::STATE_DRAFT,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
        $xelor->spells()->attach($extra->id, [
            'character_level' => 1,
            'slot_index' => 7,
            'choice_order' => 0,
        ]);

        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::xelorPath());
        $importer = app(ClassLevel1SpellSeederImporter::class);
        $first = $importer->import($catalog);

        $this->assertCount(6, $first['created']);
        $this->assertSame([], $first['skipped']);

        $aiguille = Spell::query()->where('dofusdb_id', '30842')->first();
        $this->assertNotNull($aiguille);
        $this->assertSame('Aiguille', $aiguille->name);
        $this->assertSame('3', $aiguille->pa);
        $this->assertSame('intel', $aiguille->attack_characteristic_key);
        $this->assertSame(2, $aiguille->element);
        $this->assertTrue($aiguille->po_editable);

        $raule = Spell::query()->where('dofusdb_id', '31500')->first();
        $this->assertSame('5', $raule?->pa);
        $this->assertSame(Spell::RESOLUTION_SAVING_THROW, $raule?->resolution_mode);
        $this->assertTrue($raule?->is_magic);

        $flou = Spell::query()->where('dofusdb_id', '13246')->first();
        $this->assertSame('5', $flou?->pa);
        $this->assertSame('sagesse', $flou?->save_characteristic_key);

        $this->assertSame(6, Spell::query()->where('state', Spell::STATE_PLAYABLE)->count());

        $xelor->refresh();
        $aiguillePivot = $xelor->spells()->where('spells.id', $aiguille->id)->first()?->pivot;
        $this->assertSame(1, (int) $aiguillePivot?->slot_index);
        $this->assertSame(0, (int) $aiguillePivot?->choice_order);

        $extraPivot = $xelor->spells()->where('spells.id', $extra->id)->first()?->pivot;
        $this->assertSame(
            ClassLevel1SpellSeederImporter::EXTRA_CHARACTER_LEVEL,
            (int) $extraPivot?->character_level
        );
    }

    private function seedSpellTypes(): void
    {
        foreach (['Offensif', 'Buff', 'Debuff', 'Téléportation', 'Soin'] as $name) {
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
