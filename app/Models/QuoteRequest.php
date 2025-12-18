<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequest extends Model
{
    protected $fillable = [
        'buyer_name',
        'buyer_email',
        'part_description',
        'vehicle_info',
        'status',
        'responses_count',
    ];

    protected $casts = [
        'vehicle_info' => 'array',
        'responses_count' => 'integer',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(QuoteResponse::class);
    }

    public function receivedResponses(): HasMany
    {
        return $this->responses()->where('status', 'received');
    }

    public function bestResponse(): ?QuoteResponse
    {
        return $this->receivedResponses()
            ->whereNotNull('quoted_price')
            ->orderBy('quoted_price', 'asc')
            ->first();
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function incrementResponsesCount(): void
    {
        $this->increment('responses_count');
    }
}
