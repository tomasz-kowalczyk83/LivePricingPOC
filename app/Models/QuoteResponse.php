<?php

namespace App\Models;

use App\Concerns\HasExpiry;
use App\Enums\QuoteResponseStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteResponse extends Model
{
    use HasExpiry, HasFactory;

    protected $fillable = [
        'quote_request_id',
        'trader_id',
        'quoted_price',
        'stock_available',
        'response_time_seconds',
        'notes',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'quoted_price' => 'decimal:2',
        'stock_available' => 'integer',
        'response_time_seconds' => 'integer',
        'status' => QuoteResponseStatusEnum::class,
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (QuoteResponse $quoteResponse) {
            if ($quoteResponse->expires_at === null) {
                $quoteResponse->expires_at = $quoteResponse->calculateDefaultExpiry();
            }
        });
    }

    public function calculateDefaultExpiry(): Carbon
    {
        // Load trader if not already loaded (important during creating event)
        $trader = $this->relationLoaded('trader')
            ? $this->trader
            : ($this->trader_id ? Trader::find($this->trader_id) : null);

        $minutes = $trader?->default_response_expiry_minutes
            ?? config('quotes.response.default_expiry_minutes');

        return now()->addMinutes($minutes);
    }

    public function isValid(): bool
    {
        return ! $this->isExpired()
            && $this->status === QuoteResponseStatusEnum::PENDING;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', QuoteResponseStatusEnum::PENDING);
    }

    public function scopeExpirable(Builder $query): Builder
    {
        return $query->active()->expired();
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class);
    }

    public function markAsTimeout(): void
    {
        $this->update(['status' => QuoteResponseStatusEnum::TIMEOUT]);
        // Don't increment count for timeouts - supplier didn't actively respond
    }

    public function markAsAccepted(): void
    {
        $this->update(['status' => QuoteResponseStatusEnum::ACCEPTED]);
    }

    public function markAsRejected(): void
    {
        $this->update(['status' => QuoteResponseStatusEnum::REJECTED]);
    }

    public function isPending(): bool
    {
        return $this->status === QuoteResponseStatusEnum::PENDING;
    }

    public function isSubmitted(): bool
    {
        return $this->status === QuoteResponseStatusEnum::SUBMITTED;
    }
}
