<?php
namespace Tests\Feature\Spell;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
final class SpellSaveSmokeTest extends TestCase
{
    use RefreshDatabase;
    public function test_update_with_global_props_and_redirect_edit(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'category' => 1,
            'element' => 2,
            'target_type' => 'direct',
            'ritual_available' => false,
            'state' => 'draft',
        ]);
        $response = $this->actingAs($admin)->from(route('entities.spells.edit', $spell))->patch(route('entities.spells.update', $spell), [
            'name' => 'Nom sauvé',
            'description' => 'Desc',
            'effect' => 'Effet',
            'level' => '1',
            'pa' => '3',
            'po_min' => '0',
            'po_max' => '5',
            'area' => 'point',
            'po_editable' => true,
            'sight_line' => true,
            'cast_in_line' => false,
            'cast_in_diagonal' => false,
            'target_type' => 'direct',
            'element' => 2,
            'ritual_available' => true,
            'max_stack' => 0,
            'global_cooldown' => 0,
            'cast_per_turn' => '1',
            'cast_per_target' => '0',
            'number_between_two_cast' => '0',
            'duration' => '',
            'casting_time' => '',
            'category' => 1,
            'is_magic' => true,
            'allows_reaction' => false,
            'resolution_mode' => 'attack_roll',
            'attack_characteristic_key' => '',
            'state' => 'draft',
            'read_level' => 0,
            'write_level' => 3,
            'spellTypes' => [],
            'redirect_after_update' => 'edit',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('entities.spells.edit', $spell));
        $this->assertSame('Nom sauvé', $spell->fresh()->name);
        $this->assertTrue((bool) $spell->fresh()->ritual_available);
        $this->assertSame(2, (int) $spell->fresh()->element);
    }
}
