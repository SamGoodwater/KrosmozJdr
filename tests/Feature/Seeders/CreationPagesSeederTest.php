<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\CreationPagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreationPagesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_entity_guides_and_archives_old_chartes(): void
    {
        $contribution = Page::factory()->create([
            'title' => 'Contribution',
            'slug' => 'contribution',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'menu_group' => 'Informations',
        ]);

        $oldChartes = Page::factory()->create([
            'title' => 'Créatures',
            'slug' => 'contribution-creatures',
            'parent_id' => $contribution->id,
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);

        Section::factory()->create([
            'page_id' => $oldChartes->id,
            'slug' => 'contribution-creatures-catalog',
            'template' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
            'type' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
        ]);

        Section::factory()->create([
            'page_id' => $oldChartes->id,
            'slug' => 'contribution-creatures-norms-life-points',
            'template' => SectionType::CHARACTERISTIC_NORMS->value,
            'type' => SectionType::CHARACTERISTIC_NORMS->value,
        ]);

        $hub = Page::factory()->create([
            'title' => 'Création',
            'slug' => 'creation',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'menu_group' => 'Aide',
        ]);

        $duplicate = Page::factory()->create([
            'title' => 'Créatures (catalogue)',
            'slug' => 'creation-creatures',
            'parent_id' => $hub->id,
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->seed(CreationPagesSeeder::class);

        $hub->refresh();
        $oldChartes->refresh();
        $duplicate->refresh();

        $this->assertSame('Pour les MJ', $hub->menu_group);
        $this->assertSame($hub->id, $oldChartes->parent_id);
        $this->assertTrue($oldChartes->trashed());
        $this->assertTrue($duplicate->trashed());

        $expectedSlugs = [
            'creation-classes',
            'creation-specialisations',
            'creation-sorts',
            'creation-capacites',
            'creation-monstres',
            'creation-pnj',
            'creation-equipements',
            'creation-panoplies',
            'creation-consommables',
            'creation-ressources',
            'creation-etats',
            'creation-traits',
        ];

        foreach ($expectedSlugs as $slug) {
            $this->assertDatabaseHas('pages', [
                'slug' => $slug,
                'parent_id' => $hub->id,
                'read_level' => User::ROLE_GAME_MASTER,
                'deleted_at' => null,
            ]);
        }

        $monstres = Page::query()->where('slug', 'creation-monstres')->first();
        $this->assertNotNull($monstres);
        $this->assertDatabaseHas('sections', [
            'page_id' => $monstres->id,
            'slug' => 'creation-monstres-catalog',
            'template' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
        ]);
        $this->assertDatabaseHas('sections', [
            'page_id' => $monstres->id,
            'slug' => 'creation-monstres-methode',
            'template' => SectionType::TEXT->value,
        ]);
        $methode = Section::query()->where('slug', 'creation-monstres-methode')->first();
        $this->assertNotNull($methode);
        $html = (string) ($methode->data['content'] ?? '');
        $this->assertStringContainsString('ligne faible', $html);
        $this->assertStringContainsString('Boss', $html);
        $this->assertSame(
            0,
            Section::query()
                ->where('page_id', $monstres->id)
                ->where('template', SectionType::CHARACTERISTIC_NORMS->value)
                ->count()
        );

        $capacites = Page::query()->where('slug', 'creation-capacites')->first();
        $this->assertNotNull($capacites);
        $this->assertSame(
            0,
            Section::query()
                ->where('page_id', $capacites->id)
                ->whereIn('template', [
                    SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
                    SectionType::CHARACTERISTIC_NORMS->value,
                ])
                ->count()
        );

        $equipements = Page::query()->where('slug', 'creation-equipements')->first();
        $this->assertNotNull($equipements);
        $this->assertDatabaseHas('sections', [
            'page_id' => $equipements->id,
            'slug' => 'creation-equipements-table',
            'template' => SectionType::EQUIPMENT_BONUS_TABLE->value,
        ]);
        $this->assertDatabaseHas('sections', [
            'page_id' => $equipements->id,
            'slug' => 'creation-equipements-methode',
            'template' => SectionType::TEXT->value,
        ]);
        $this->assertDatabaseHas('sections', [
            'page_id' => $equipements->id,
            'slug' => 'creation-equipements-catalog',
            'template' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
        ]);
    }
}
