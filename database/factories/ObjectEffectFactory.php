<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ObjectEffectAction;
use App\Models\ObjectEffect;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ObjectEffect>
 *
 * Préférer `$item->objectEffects()->create([...])` (morphTo). La factory sert surtout aux données par défaut.
 */
class ObjectEffectFactory extends Factory
{
    protected $model = ObjectEffect::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'action' => ObjectEffectAction::Add,
            'characteristic_id' => null,
            'monster_id' => null,
            'value' => fake()->numberBetween(1, 10),
        ];
    }
}
