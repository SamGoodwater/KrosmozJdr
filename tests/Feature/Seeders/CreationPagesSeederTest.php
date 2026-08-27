<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\CreationPagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreationPagesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_reparents_contribution_chartes_and_archives_creation_duplicates(): void
    {
        $contribution = Page::factory()->create([
            'title' => 'Contribution',
            'slug' => 'contribution',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'menu_group' => 'Informations',
        ]);

        $creatures = Page::factory()->create([
            'title' => 'Créatures',
            'slug' => 'contribution-creatures',
            'parent_id' => $contribution->id,
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
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
        $creatures->refresh();
        $duplicate->refresh();

        $this->assertSame('Pour les MJ', $hub->menu_group);
        $this->assertSame($hub->id, $creatures->parent_id);
        $this->assertSame(User::ROLE_GAME_MASTER, $creatures->read_level);
        $this->assertTrue($duplicate->trashed());

        $this->assertDatabaseHas('pages', [
            'slug' => 'creation-equipements',
            'parent_id' => $hub->id,
            'read_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->assertDatabaseHas('sections', [
            'page_id' => $creatures->id,
            'slug' => 'contribution-creatures-catalog',
            'template' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
        ]);

        $equipements = Page::query()->where('slug', 'creation-equipements')->first();
        $this->assertNotNull($equipements);
        $this->assertDatabaseHas('sections', [
            'page_id' => $equipements->id,
            'slug' => 'creation-equipements-table',
            'template' => SectionType::EQUIPMENT_BONUS_TABLE->value,
        ]);
    }
}
