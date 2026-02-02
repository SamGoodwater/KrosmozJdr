<?php

namespace Tests\Unit\Scrapping\Core;

use App\Models\Characteristic;
use App\Models\CharacteristicEntity;
use App\Services\Characteristic\CharacteristicService;
use App\Services\Scrapping\Core\Validation\ValidationResult;
use App\Services\Scrapping\Core\Validation\ValidationService;
use Tests\TestCase;

/**
 * Tests unitaires pour ValidationService (règles BDD, limites, champs requis, entités).
 * Utilise le CharacteristicService réel et la BDD (RefreshDatabase + création Characteristic / CharacteristicEntity).
 */
class ValidationServiceTest extends TestCase
{
    private ValidationService $service;
    private CharacteristicService $characteristicService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ValidationService::class);
        $this->characteristicService = app(CharacteristicService::class);
    }

    private function clearCharacteristicsCache(): void
    {
        $this->characteristicService->clearCache();
    }

    public function test_validate_ok_when_no_rules_defined(): void
    {
        $this->clearCharacteristicsCache();
        $convertedData = [
            'creatures' => ['name' => 'Test', 'level' => 5],
            'monsters' => ['dofusdb_id' => '31'],
        ];

        $result = $this->service->validate($convertedData, 'monster');

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertSame([], $result->getErrors());
    }

    public function test_validate_fails_when_required_field_missing(): void
    {
        Characteristic::create([
            'id' => 'test_name',
            'name' => 'Nom',
            'type' => 'string',
            'sort_order' => 0,
            'applies_to' => ['monster'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'test_name',
            'entity' => 'monster',
            'required' => true,
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = [
            'creatures' => ['level' => 5],
            'monsters' => ['dofusdb_id' => '31'],
        ];

        $result = $this->service->validate($convertedData, 'monster');

        $this->assertFalse($result->isValid());
        $errors = $result->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('test_name', $errors[0]['path'] ?? null);
        $this->assertStringContainsString('requis', $errors[0]['message'] ?? '');
    }

    public function test_validate_ok_when_required_field_present(): void
    {
        Characteristic::create([
            'id' => 'test_name_ok',
            'name' => 'Nom',
            'type' => 'string',
            'sort_order' => 0,
            'applies_to' => ['monster'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'test_name_ok',
            'entity' => 'monster',
            'required' => true,
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = [
            'creatures' => ['test_name_ok' => 'Bouftou', 'level' => 5],
            'monsters' => ['dofusdb_id' => '31'],
        ];

        $result = $this->service->validate($convertedData, 'monster');

        $this->assertTrue($result->isValid());
    }

    public function test_validate_fails_when_int_out_of_limits(): void
    {
        Characteristic::create([
            'id' => 'test_level',
            'name' => 'Niveau',
            'type' => 'int',
            'sort_order' => 0,
            'applies_to' => ['monster'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'test_level',
            'entity' => 'monster',
            'min' => 1,
            'max' => 50,
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = [
            'creatures' => ['name' => 'Bouftou', 'test_level' => 999],
            'monsters' => [],
        ];

        $result = $this->service->validate($convertedData, 'monster');

        $this->assertFalse($result->isValid());
        $errors = $result->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('test_level', $errors[0]['path'] ?? null);
    }

    public function test_validate_ok_when_int_within_limits(): void
    {
        Characteristic::create([
            'id' => 'test_level_ok',
            'name' => 'Niveau',
            'type' => 'int',
            'sort_order' => 0,
            'applies_to' => ['monster'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'test_level_ok',
            'entity' => 'monster',
            'min' => 1,
            'max' => 50,
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = [
            'creatures' => ['name' => 'Bouftou', 'test_level_ok' => 10],
            'monsters' => [],
        ];

        $result = $this->service->validate($convertedData, 'monster');

        $this->assertTrue($result->isValid());
    }

    public function test_resolves_breed_to_class_validation_rules(): void
    {
        Characteristic::create([
            'id' => 'test_class_name',
            'name' => 'Nom classe',
            'type' => 'string',
            'sort_order' => 0,
            'applies_to' => ['class'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'test_class_name',
            'entity' => 'class',
            'required' => true,
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = ['breeds' => []];

        $result = $this->service->validate($convertedData, 'breed');

        $this->assertFalse($result->isValid());
        $errors = $result->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('test_class_name', $errors[0]['path'] ?? null);
    }

    public function test_validate_breed_ok_when_class_required_met(): void
    {
        Characteristic::create([
            'id' => 'test_class_name_ok',
            'name' => 'Nom classe',
            'type' => 'string',
            'sort_order' => 0,
            'applies_to' => ['class'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'test_class_name_ok',
            'entity' => 'class',
            'required' => true,
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = ['breeds' => ['test_class_name_ok' => 'Feca']];

        $result = $this->service->validate($convertedData, 'breed');

        $this->assertTrue($result->isValid());
    }

    public function test_validate_fails_when_value_available_rejects_value(): void
    {
        Characteristic::create([
            'id' => 'size',
            'name' => 'Taille',
            'type' => 'array',
            'sort_order' => 0,
            'applies_to' => ['monster'],
            'value_available' => ['tiny', 'small', 'medium', 'large', 'huge'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'size',
            'entity' => 'monster',
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = [
            'monsters' => ['size' => 'invalid_size'],
        ];

        $result = $this->service->validate($convertedData, 'monster');

        $this->assertFalse($result->isValid());
        $errors = $result->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('size', $errors[0]['path'] ?? '');
    }

    public function test_validate_ok_when_value_available_accepts_value(): void
    {
        Characteristic::create([
            'id' => 'size_ok',
            'name' => 'Taille',
            'type' => 'array',
            'sort_order' => 0,
            'applies_to' => ['monster'],
            'value_available' => ['tiny', 'small', 'medium', 'large', 'huge'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'size_ok',
            'entity' => 'monster',
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = [
            'monsters' => ['size_ok' => 'medium'],
        ];

        $result = $this->service->validate($convertedData, 'monster');

        $this->assertTrue($result->isValid());
    }

    public function test_validate_accepts_chance_via_luck_alias(): void
    {
        Characteristic::create([
            'id' => 'chance',
            'name' => 'Chance',
            'type' => 'int',
            'db_column' => 'chance',
            'sort_order' => 0,
            'applies_to' => ['monster'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'chance',
            'entity' => 'monster',
            'required' => true,
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = [
            'creatures' => ['luck' => 10],
            'monsters' => [],
        ];

        $result = $this->service->validate($convertedData, 'monster');

        $this->assertTrue($result->isValid());
    }

    public function test_validate_spell_uses_spell_entity_rules(): void
    {
        Characteristic::create([
            'id' => 'spell_name',
            'name' => 'Nom sort',
            'type' => 'string',
            'sort_order' => 0,
            'applies_to' => ['spell'],
        ]);
        CharacteristicEntity::create([
            'characteristic_id' => 'spell_name',
            'entity' => 'spell',
            'required' => true,
        ]);
        $this->clearCharacteristicsCache();

        $convertedData = ['spells' => []];

        $result = $this->service->validate($convertedData, 'spell');

        $this->assertFalse($result->isValid());
        $errors = $result->getErrors();
        $this->assertSame('spell_name', $errors[0]['path'] ?? null);
    }
}
