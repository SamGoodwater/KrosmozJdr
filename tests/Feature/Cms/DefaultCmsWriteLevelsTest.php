<?php

declare(strict_types=1);

namespace Tests\Feature\Cms;

use App\Enums\SectionType;
use App\Models\Entity\Spell;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Policies\Entity\SpellPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase A — décision Q6 : par défaut, édition CMS au moins jusqu’au niveau MJ ; exemple entité playable lisible invité lorsque prévu par la policy.
 */
class DefaultCmsWriteLevelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_playable_spell_with_guest_read_level(): void
    {
        /** @phpstan-ignore-next-line */
        $spell = Spell::factory()->create([
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $policy = new SpellPolicy;
        /** @phpstan-ignore-next-line */
        $this->assertTrue($policy->view(null, $spell));
    }

    public function test_new_page_without_levels_defaults_guest_read_game_master_write(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $slug = 'default-write-'.uniqid();

        $this->actingAs($admin)->postJson(route('pages.store'), [
            'title' => 'Page niveaux défaut',
            'slug' => $slug,
            'state' => Page::STATE_DRAFT,
        ]);

        $this->assertDatabaseHas('pages', [
            'slug' => $slug,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
    }

    public function test_new_section_without_levels_defaults_guest_read_game_master_write(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->actingAs($admin)->post(route('sections.store'), [
            'page_id' => $page->id,
            'type' => SectionType::TEXT->value,
            'params' => [
                'content' => 'Contenu par défaut',
            ],
        ]);

        /** @var Section|null $created */
        $created = Section::query()->latest('id')->first();
        $this->assertNotNull($created);
        $this->assertSame(User::ROLE_GUEST, (int) $created->read_level);
        $this->assertSame(User::ROLE_GAME_MASTER, (int) $created->write_level);
    }
}
