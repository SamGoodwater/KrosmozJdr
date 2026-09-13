<?php

declare(strict_types=1);

namespace Tests\Unit\Seeder;

use App\Services\Seeder\Spell\ClassLevel1SpellCatalog;
use Tests\TestCase;

final class ClassLevel1SpellCatalogTest extends TestCase
{
    public function test_iop_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load();

        $this->assertSame('Iop', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $slots = [];
        foreach ($entries as $entry) {
            $slots[$entry['slot_index']][] = $entry['choice_order'];
        }
        ksort($slots);
        $this->assertSame([1, 2, 3], array_keys($slots));
        foreach ($slots as $orders) {
            sort($orders);
            $this->assertSame([0, 1], $orders);
        }

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Pression', 'Attaque Naturelle', 'Fendoir', 'Intimidation', 'Bond', 'Concentration'],
            $names
        );
        $this->assertSame('jdr:attaque-naturelle', $entries[1]['official_id']);
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('5', $entries[2]['pa']);
    }

    public function test_cra_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::craPath());

        $this->assertSame('Crâ', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $slots = [];
        foreach ($entries as $entry) {
            $slots[$entry['slot_index']][] = $entry['choice_order'];
        }
        ksort($slots);
        $this->assertSame([1, 2, 3], array_keys($slots));
        foreach ($slots as $orders) {
            sort($orders);
            $this->assertSame([0, 1], $orders);
        }

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Flèche Cinglante', 'Flèche Glacée', 'Flèche Explosive', 'Tir Perforant', 'Œil de Lynx', 'Tir de Recul'],
            $names
        );
        $this->assertSame('jdr:oeil-de-lynx', $entries[4]['official_id']);
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame('2', $entries[0]['po_min']);
        $this->assertTrue($entries[0]['po_editable']);
    }

    public function test_eniripsa_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::eniripsaPath());

        $this->assertSame('Eniripsa', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $slots = [];
        foreach ($entries as $entry) {
            $slots[$entry['slot_index']][] = $entry['choice_order'];
        }
        ksort($slots);
        $this->assertSame([1, 2, 3], array_keys($slots));
        foreach ($slots as $orders) {
            sort($orders);
            $this->assertSame([0, 1], $orders);
        }

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Mot Vivifiant', 'Mot d’Amitié', 'Mot Interdit', 'Mot Tapageur', 'Mot de Frayeur', 'Mot d’Envol'],
            $names
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame('auto_success', $entries[0]['resolution_mode']);
        $this->assertSame('1d4+Sagesse', $entries[0]['sub_effects'][0]['params']['value']);
        $this->assertSame('2d4+Sagesse', $entries[2]['sub_effects'][0]['params']['value']);
    }

    public function test_sram_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sramPath());

        $this->assertSame('Sram', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Sournoiserie', 'Cruauté', 'Piège Sournois', 'Invisibilité', 'Dérobade', 'Peur'],
            $names
        );
        $this->assertSame('trap', $entries[2]['target_type']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame('5', $entries[3]['pa']);
        $this->assertSame('3', $entries[0]['pa']);
    }

    public function test_xelor_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::xelorPath());

        $this->assertSame('Xélor', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Aiguille', 'Gelure', 'Raulebaque', 'Flou Temporel', 'Téléportation', 'Permutation'],
            $names
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertTrue($entries[0]['po_editable']);
        $this->assertSame('intel', $entries[0]['attack_characteristic_key']);
        $this->assertSame('chance', $entries[1]['attack_characteristic_key']);
    }

    public function test_feca_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::fecaPath());

        $this->assertSame('Féca', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Attaque Naturelle', 'Rempart', 'Glyphe Enflammé', 'Escapade', 'Bouclier Féca', 'Armure Aqueuse'],
            $names
        );
        $this->assertSame('glyph', $entries[2]['target_type']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('protéger', $entries[4]['sub_effects'][0]['slug']);
    }

    public function test_osamodas_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::osamodasPath());

        $this->assertSame('Osamodas', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Serres du Vautour', 'Griffes du Chtigre', 'Déplumage', 'Frappe du Craqueleur', 'Tofu', 'Dragoune'],
            $names
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame('agi', $entries[0]['attack_characteristic_key']);
        $this->assertSame(['Invocation'], $entries[4]['types']);
    }

    public function test_enutrof_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::enutrofPath());

        $this->assertSame('Enutrof', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);

        $names = array_column($entries, 'name');
        $this->assertSame(
            ['Lancer de Pièces', 'Roulage de Pelle', 'Pelle Aurifère', 'Lancer de Pelle', 'Maladresse', 'Souterrain'],
            $names
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame('chance', $entries[0]['attack_characteristic_key']);
        $this->assertSame('saving_throw', $entries[4]['resolution_mode']);
    }

    public function test_sadida_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sadidaPath());

        $this->assertSame('Sadida', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);
        $this->assertSame(
            ['Ronce', 'Larme de Sadida', 'Tremblement', 'Vent Empoisonné', 'Poupée Sadida', 'Ronce Apaisante'],
            array_column($entries, 'name')
        );
        $this->assertSame('3', $entries[0]['pa']);
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame(['Invocation'], $entries[4]['types']);
        $this->assertSame('soigner', $entries[5]['sub_effects'][0]['slug']);
    }

    public function test_sacrieur_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::sacrieurPath());

        $this->assertSame('Sacrieur', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);
        $this->assertSame(
            ['Punition', 'Absorption', 'Folie Sanguinaire', 'Châtiment', 'Attirance', 'Sacrifice'],
            array_column($entries, 'name')
        );
        $this->assertSame('1d4', $entries[1]['sub_effects'][0]['params']['life_steal_formula']);
        $this->assertSame('pull', $entries[4]['sub_effects'][0]['params']['movement_kind']);
    }

    public function test_pandawa_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::pandawaPath());

        $this->assertSame('Pandawa', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);
        $this->assertSame(
            ['Poing Enflammé', 'Vague à Lame', 'Pandatak', 'Flasque Explosive', 'Picole', 'Chamrak'],
            array_column($entries, 'name')
        );
        $this->assertSame('5', $entries[2]['pa']);
        $this->assertSame('booster', $entries[4]['sub_effects'][0]['slug']);
        $this->assertSame('push', $entries[5]['sub_effects'][0]['params']['movement_kind']);
    }

    public function test_ecaflip_catalog_has_six_spells_in_three_slots(): void
    {
        $catalog = ClassLevel1SpellCatalog::load(ClassLevel1SpellCatalog::ecaflipPath());

        $this->assertSame('Ecaflip', $catalog->breedName());
        $entries = $catalog->entries();
        $this->assertCount(6, $entries);
        $this->assertSame(
            ['Topkaj', 'Yams', 'Pelotage', 'Kraps', 'Bond du Félin', 'Entrechat'],
            array_column($entries, 'name')
        );
        $this->assertSame('intel', $entries[0]['attack_characteristic_key']);
        $this->assertSame('chance', $entries[1]['attack_characteristic_key']);
        $this->assertSame('auto_success', $entries[4]['resolution_mode']);
    }

    public function test_added_class_catalogs_have_six_spells_in_three_slots(): void
    {
        $expected = [
            [ClassLevel1SpellCatalog::roublardPath(), 'Roublard', ['Pulsar', 'Espingole', 'Explobombe', 'Sismobombe', 'Détonateur', 'Botte']],
            [ClassLevel1SpellCatalog::zobalPath(), 'Zobal', ['Brincadeira', 'Parafuso', 'Catalepsie', 'Appui', 'Plastron', 'Cavalcade']],
            [ClassLevel1SpellCatalog::steamerPath(), 'Steamer', ['Torpille', 'Longue-vue', 'Sabotage', 'Aspiration', 'Harponneuse', 'Gardienne']],
            [ClassLevel1SpellCatalog::eliotropePath(), 'Eliotrope', ['Affront', 'Audace', 'Commotion', 'Rayon de Wakfu', 'Portail', 'Cicatrisation']],
            [ClassLevel1SpellCatalog::huppermagePath(), 'Huppermage', ['Lance-flamme', 'Stalagmite', 'Météore', 'Onde Sismique', 'Éther', 'Runification']],
            [ClassLevel1SpellCatalog::ouginakPath(), 'Ouginak', ['Molosse', 'Charogne', 'Os à Moelle', 'Lance-roquet', 'Traque', 'Amarok']],
            [ClassLevel1SpellCatalog::forgelancePath(), 'Forgelance', ['Estoc Brûlant', 'Lance du Lac', "Volée d'Airain", 'Effondrement', 'Charge Héroïque', 'Phalange']],
        ];

        foreach ($expected as [$path, $breed, $names]) {
            $catalog = ClassLevel1SpellCatalog::load($path);
            $this->assertSame($breed, $catalog->breedName());
            $entries = $catalog->entries();
            $this->assertCount(6, $entries, $breed);
            $this->assertSame($names, array_column($entries, 'name'), $breed);
            $this->assertSame('3', $entries[0]['pa'], $breed);
            $this->assertSame('5', $entries[2]['pa'], $breed);
        }
    }

    public function test_load_all_finds_all_class_kits(): void
    {
        $catalogs = ClassLevel1SpellCatalog::loadAllInDirectory();
        $this->assertCount(38, $catalogs);

        $breeds = array_values(array_unique(array_map(
            static fn (ClassLevel1SpellCatalog $c): string => $c->breedName(),
            $catalogs
        )));
        sort($breeds);
        $this->assertSame(
            ['Crâ', 'Ecaflip', 'Eliotrope', 'Eniripsa', 'Enutrof', 'Forgelance', 'Féca', 'Huppermage', 'Iop', 'Osamodas', 'Ouginak', 'Pandawa', 'Roublard', 'Sacrieur', 'Sadida', 'Sram', 'Steamer', 'Xélor', 'Zobal'],
            $breeds
        );
    }

    public function test_progression_catalogs_have_eighteen_spells_on_nine_levels(): void
    {
        $levels = [3, 4, 5, 7, 8, 10, 11, 13, 14];
        $files = glob(ClassLevel1SpellCatalog::directory().'/*-progression.json') ?: [];
        $this->assertCount(19, $files);

        foreach ($files as $file) {
            $catalog = ClassLevel1SpellCatalog::load($file);
            $entries = $catalog->entries();
            $this->assertCount(18, $entries, $catalog->breedName());
            $got = [];
            foreach ($entries as $entry) {
                $got[] = [$entry['character_level'], $entry['choice_order']];
            }
            $expected = [];
            foreach ($levels as $level) {
                $expected[] = [$level, 0];
                $expected[] = [$level, 1];
            }
            $this->assertSame($expected, $got, $catalog->breedName());
        }
    }
}
