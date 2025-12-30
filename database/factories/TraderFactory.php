<?php

namespace Database\Factories;

use App\Enums\TraderTypeEnum;
use App\Models\Company;
use App\Models\Trader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trader>
 */
class TraderFactory extends Factory
{
    protected $model = Trader::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'company_id' => Company::factory(),
            'type' => fake()->randomElement(TraderTypeEnum::cases()),
            'is_active' => true,
            'default_request_expiry_minutes' => null,
            'default_response_expiry_minutes' => null,
        ];
    }

    public function buyer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TraderTypeEnum::BUYER,
        ]);
    }

    public function supplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => TraderTypeEnum::SUPPLIER,
        ]);
    }

    public function withRequestExpiry(int $minutes): static
    {
        return $this->state(fn (array $attributes) => [
            'default_request_expiry_minutes' => $minutes,
        ]);
    }

    public function withResponseExpiry(int $minutes): static
    {
        return $this->state(fn (array $attributes) => [
            'default_response_expiry_minutes' => $minutes,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
