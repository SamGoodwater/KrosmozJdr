<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LoadingTip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoadingTip>
 */
class LoadingTipFactory extends Factory
{
    protected $model = LoadingTip::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body' => fake()->sentence(8),
            'url' => null,
            'featured' => false,
            'is_active' => true,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['featured' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function withUrl(?string $url = null): static
    {
        return $this->state(fn () => [
            'url' => $url ?? 'https://example.com/tip',
        ]);
    }
}
