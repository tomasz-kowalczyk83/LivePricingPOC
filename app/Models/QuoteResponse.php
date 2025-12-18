<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteResponse extends Model
{
    protected $fillable = [
        'quote_request_id',
        'supplier_id',
        'quoted_price',
        'stock_available',
        'response_time_seconds',
        'notes',
        'status',
    ];

    protected $casts = [
        'quoted_price' => 'decimal:2',
        'stock_available' => 'integer',
        'response_time_seconds' => 'integer',
    ];

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function markAsReceived(): void
    {
        $this->update(['status' => 'received']);
    }

    public function markAsTimeout(): void
    {
        $this->update(['status' => 'timeout']);
    }
}
