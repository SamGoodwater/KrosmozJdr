<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\EntityGenerationProfile;
use App\Services\GenerativeAi\Specializations\MonsterSpecialization;
use Tests\TestCase;

final class MonsterSpecializationTest extends TestCase
{
    public function test_validate_requires_two_to_three_named_spells(): void
    {
        $spec = app(MonsterSpecialization::class);
        $profile = $this->profile();
        $request = new ConversionRequest(action: 'encounter', entityType: 'monster', entityId: 1);

        $tooFew = $spec->validate(['spells' => [['name' => 'A', 'effect' => '1d4']]], $request, $profile);
        $this->assertNotSame([], $tooFew);

        $ok = $spec->validate([
            'monster' => [],
            'spells' => [
                ['name' => 'Bec', 'effect' => '1d6 Air', 'pa' => 3],
                ['name' => 'Picore', 'effect' => '1d4 Air', 'pa' => 2],
            ],
        ], $request, $profile);
        $this->assertSame([], $ok);
    }

    public function test_schema_is_writable_only_for_monster_keys(): void
    {
        $schema = app(MonsterSpecialization::class)->jsonSchema($this->profile(['description']));
        $this->assertArrayHasKey('spells', $schema['properties']);
        $this->assertArrayHasKey('description', $schema['properties']['monster']['properties']);
        $this->assertArrayNotHasKey('name', $schema['properties']['monster']['properties']);
        $this->assertFalse($schema['additionalProperties']);
    }

    /**
     * @param  list<string>  $writable
     */
    private function profile(array $writable = []): EntityGenerationProfile
    {
        return new EntityGenerationProfile(
            entity: 'monster',
            hasDofusSource: true,
            frozenFields: '*',
            writableFields: $writable,
            frozenCharacteristics: '*',
            writableCharacteristics: [],
            exampleIds: [],
            extra: [],
        );
    }
}
