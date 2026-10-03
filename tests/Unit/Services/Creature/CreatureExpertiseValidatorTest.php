<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Creature;

use App\Services\Creature\CreatureExpertiseValidator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreatureExpertiseValidatorTest extends TestCase
{
    private CreatureExpertiseValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new CreatureExpertiseValidator;
    }

    #[Test]
    public function max_expertises_follow_level_tiers(): void
    {
        $this->assertSame(0, $this->validator->maxExpertisesForLevel(8));
        $this->assertSame(1, $this->validator->maxExpertisesForLevel(9));
        $this->assertSame(1, $this->validator->maxExpertisesForLevel(14));
        $this->assertSame(2, $this->validator->maxExpertisesForLevel(15));
        $this->assertSame(2, $this->validator->maxExpertisesForLevel(19));
        $this->assertSame(3, $this->validator->maxExpertisesForLevel(20));
    }

    #[Test]
    public function rejects_expertise_before_level_nine(): void
    {
        $masteries = ['athletisme_mastery' => 2];
        $errors = $this->validator->validate(5, $masteries);

        $this->assertNotEmpty($errors);
    }

    #[Test]
    public function allows_one_expertise_at_level_twelve(): void
    {
        $masteries = [
            'athletisme_mastery' => 2,
            'discretion_mastery' => 1,
        ];
        $errors = $this->validator->validate(12, $masteries);

        $this->assertSame([], $errors);
    }

    #[Test]
    public function rejects_three_expertises_at_level_fifteen(): void
    {
        $masteries = [
            'athletisme_mastery' => 2,
            'discretion_mastery' => 2,
            'arcane_mastery' => 2,
        ];
        $errors = $this->validator->validate(15, $masteries);

        $this->assertNotEmpty($errors);
    }
}
