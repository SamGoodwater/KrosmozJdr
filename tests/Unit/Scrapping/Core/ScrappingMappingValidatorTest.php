<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core;

use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Config\ScrappingMappingValidator;
use App\Services\Scrapping\Core\Conversion\FormatterApplicator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SeedsScrappingPipeline;
use Tests\TestCase;

class ScrappingMappingValidatorTest extends TestCase
{
    use RefreshDatabase, SeedsScrappingPipeline;

    private ScrappingMappingValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedScrappingPipeline();
        $this->validator = new ScrappingMappingValidator(
            app(FormatterApplicator::class),
            app(CharacteristicGetterService::class),
            app(CharacteristicFormulaService::class),
        );
    }

    public function test_it_rejects_unknown_formatter(): void
    {
        $errors = $this->validator->validate('monster', [[
            'from' => ['path' => 'grades.0.level'],
            'to' => [['model' => 'creatures', 'field' => 'level']],
            'formatters' => [['name' => 'formatterAbsent', 'args' => []]],
        ]]);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Formatter inconnu', $errors[0]['message']);
    }

    public function test_it_accepts_characteristic_driven_mapping(): void
    {
        $errors = $this->validator->validate('monster', [[
            'from' => ['path' => 'grades.0.strength'],
            'to' => [['model' => 'creatures', 'field' => 'strength']],
            'formatters' => [['name' => 'convertCharacteristic', 'args' => []]],
            'characteristic_key' => 'strength_creature',
        ]]);

        $this->assertSame([], $errors);
    }
}
