<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Spell;
use App\Services\GenerativeAi\AllowlistWriter;
use InvalidArgumentException;
use Tests\TestCase;

final class AllowlistWriterTest extends TestCase
{
    public function test_writes_only_allowed_fields_and_forces_auto_state(): void
    {
        $spell = Spell::factory()->create([
            'name' => 'Pression',
            'effect' => 'ancien',
            'state' => EntityState::Draft->value,
            'auto_update' => true,
        ]);

        app(AllowlistWriter::class)->apply($spell, ['effect'], [
            'effect' => '1d8 Terre',
            'name' => 'HACK',
            'id' => 999,
        ]);

        $spell->refresh();
        $this->assertSame('1d8 Terre', $spell->effect);
        $this->assertSame('Pression', $spell->name);
        $this->assertSame(EntityState::Auto->value, $spell->state);
        $this->assertFalse($spell->auto_update);
    }

    public function test_rejects_state_in_llm_payload(): void
    {
        $spell = Spell::factory()->create(['state' => EntityState::Draft->value]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('state ni auto_update');

        app(AllowlistWriter::class)->apply($spell, ['effect', 'state'], [
            'effect' => 'x',
            'state' => EntityState::Playable->value,
        ]);
    }
}
