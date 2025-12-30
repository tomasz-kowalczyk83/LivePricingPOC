<?php

namespace App\Models;

use App\Concerns\HasExpiry;
use App\Enums\QuoteRequestStatusEnum;
use App\Enums\QuoteResponseStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequest extends Model
{
    use HasExpiry, HasFactory;

    protected $fillable = [
        'trader_id',
        'status',
        'is_anonymous',
        'responses_count',
        'expected_responses_count',
        'expires_at',
    ];

    protected $casts = [
        'responses_count' => 'integer',
        'expected_responses_count' => 'integer',
        'is_anonymous' => 'boolean',
        'status' => QuoteRequestStatusEnum::class,
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (QuoteRequest $quoteRequest) {
            if ($quoteRequest->expires_at === null) {
                $quoteRequest->expires_at = $quoteRequest->calculateDefaultExpiry();
            }
        });
    }

    public function calculateDefaultExpiry(): Carbon
    {
        // Load trader if not already loaded (important during creating event)
        $trader = $this->relationLoaded('trader')
            ? $this->trader
            : ($this->trader_id ? Trader::find($this->trader_id) : null);

        $minutes = $trader?->default_request_expiry_minutes
            ?? config('quotes.request.default_expiry_minutes');

        return now()->addMinutes($minutes);
    }

    public function isValid(): bool
    {
        return ! $this->isExpired()
            && in_array($this->status, [
                QuoteRequestStatusEnum::PENDING,
                QuoteRequestStatusEnum::PROCESSING,
            ], true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            QuoteRequestStatusEnum::PENDING,
            QuoteRequestStatusEnum::PROCESSING,
        ]);
    }

    public function scopeExpirable(Builder $query): Builder
    {
        return $query->active()->expired();
    }

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuoteResponse::class);
    }

    public function submittedResponses(): HasMany
    {
        return $this->responses()->where('status', QuoteResponseStatusEnum::SUBMITTED);
    }

    public function bestResponse(): ?QuoteResponse
    {
        return $this->submittedResponses()
            ->whereNotNull('quoted_price')
            ->orderBy('quoted_price', 'asc')
            ->first();
    }

    public function acceptedResponse(): ?QuoteResponse
    {
        return $this->responses()
            ->where('status', QuoteResponseStatusEnum::ACCEPTED)
            ->first();
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => QuoteRequestStatusEnum::PROCESSING]);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => QuoteRequestStatusEnum::COMPLETED]);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => QuoteRequestStatusEnum::CANCELLED]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => QuoteRequestStatusEnum::EXPIRED]);
    }

    public function isProcessing(): bool
    {
        return $this->status === QuoteRequestStatusEnum::PROCESSING;
    }

    public function getBuyerDisplayName(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous Buyer';
        }

        return $this->trader?->name ?? 'Unknown';
    }
}
