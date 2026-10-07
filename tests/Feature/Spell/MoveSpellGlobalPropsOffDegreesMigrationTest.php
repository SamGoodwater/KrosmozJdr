<?php

declare(strict_types=1);

namespace Tests\Feature\Spell;

use App\Models\Entity\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Vérifie la rétro-copie element / target_type / ritual_available du degré 1 vers le sort.
 * Les colonnes sont déjà droppées après migrate ; on simule la logique de copie.
 */
final class MoveSpellGlobalPropsOffDegreesMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_spell_degrees_no_longer_have_global_property_columns(): void
    {
        $this->assertTrue(Schema::hasTable('spell_degrees'));
        $this->assertFalse(Schema::hasColumn('spell_degrees', 'element'));
        $this->assertFalse(Schema::hasColumn('spell_degrees', 'target_type'));
        $this->assertFalse(Schema::hasColumn('spell_degrees', 'ritual_available'));

        $this->assertTrue(Schema::hasColumn('spells', 'element'));
        $this->assertTrue(Schema::hasColumn('spells', 'target_type'));
        $this->assertTrue(Schema::hasColumn('spells', 'ritual_available'));
    }

    public function test_retro_copy_logic_fills_null_spell_fields_from_degree_one(): void
    {
        $spell = Spell::factory()->create([
            'element' => null,
            'target_type' => null,
            'ritual_available' => null,
        ]);

        // Simule l’état pré-migration (colonnes déjà absentes : on applique la même logique de fill).
        $degreeOne = (object) [
            'spell_id' => $spell->id,
            'element' => 4,
            'target_type' => 'trap',
            'ritual_available' => 1,
        ];

        $updates = [];
        if ($spell->element === null && $degreeOne->element !== null) {
            $updates['element'] = $degreeOne->element;
        }
        if (($spell->target_type === null || $spell->target_type === '')
            && $degreeOne->target_type !== null
            && $degreeOne->target_type !== ''
        ) {
            $updates['target_type'] = $degreeOne->target_type;
        }
        if ($spell->ritual_available === null && $degreeOne->ritual_available !== null) {
            $updates['ritual_available'] = (bool) $degreeOne->ritual_available;
        }

        DB::table('spells')->where('id', $spell->id)->update($updates);
        $spell->refresh();

        $this->assertSame(4, (int) $spell->element);
        $this->assertSame('trap', $spell->target_type);
        $this->assertTrue((bool) $spell->ritual_available);
    }
}
