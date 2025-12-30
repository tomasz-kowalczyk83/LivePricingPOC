<?php

namespace Database\Factories;

use App\Enums\QuoteResponseStatusEnum;
use App\Models\QuoteRequest;
use App\Models\QuoteResponse;
use App\Models\Trader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteResponse>
 */
class QuoteResponseFactory extends Factory
{
    protected $model = QuoteResponse::class;

    public function definition(): array
    {
        return [
            'quote_request_id' => QuoteRequest::factory(),
            'trader_id' => Trader::factory()->supplier(),
            'quoted_price' => fake()->randomFloat(2, 10, 1000),
            'stock_available' => fake()->numberBetween(0, 100),
            'response_time_seconds' => fake()->numberBetween(1, 120),
            'notes' => fake()->optional()->sentence(),
            'status' => QuoteResponseStatusEnum::PENDING,
            'expires_at' => now()->addMinutes(config('quotes.response.default_expiry_minutes', 60)),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteResponseStatusEnum::PENDING,
        ]);
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteResponseStatusEnum::SUBMITTED,
        ]);
    }

    public function timeout(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteResponseStatusEnum::TIMEOUT,
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteResponseStatusEnum::ACCEPTED,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMinutes(5),
        ]);
    }

    public function expiringIn(int $minutes): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addMinutes($minutes),
        ]);
    }

    public function neverExpires(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => null,
        ]);
    }
}
