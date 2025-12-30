<?php

namespace Database\Factories;

use App\Enums\QuoteRequestStatusEnum;
use App\Models\QuoteRequest;
use App\Models\Trader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteRequest>
 */
class QuoteRequestFactory extends Factory
{
    protected $model = QuoteRequest::class;

    public function definition(): array
    {
        return [
            'trader_id' => Trader::factory()->buyer(),
            'status' => QuoteRequestStatusEnum::PENDING,
            'responses_count' => 0,
            'expires_at' => now()->addMinutes(config('quotes.request.default_expiry_minutes', 1440)),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteRequestStatusEnum::PENDING,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteRequestStatusEnum::PROCESSING,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteRequestStatusEnum::COMPLETED,
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
