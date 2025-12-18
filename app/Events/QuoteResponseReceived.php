<?php

namespace App\Events;

use App\Models\QuoteResponse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteResponseReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public QuoteResponse $quoteResponse
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('quote-request.' . $this->quoteResponse->quote_request_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->quoteResponse->id,
            'supplier_id' => $this->quoteResponse->supplier_id,
            'supplier_name' => $this->quoteResponse->supplier->name,
            'quoted_price' => $this->quoteResponse->quoted_price,
            'stock_available' => $this->quoteResponse->stock_available,
            'response_time_seconds' => $this->quoteResponse->response_time_seconds,
            'notes' => $this->quoteResponse->notes,
            'status' => $this->quoteResponse->status,
        ];
    }
}
