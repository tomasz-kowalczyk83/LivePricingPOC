<?php

namespace App\Events;

use App\Models\QuoteResponse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteResponseTimedOut implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public QuoteResponse $quoteResponse
    ) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('quote-request.'.$this->quoteResponse->quote_request_id),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->quoteResponse->id,
            'quote_request_id' => $this->quoteResponse->quote_request_id,
            'trader_id' => $this->quoteResponse->trader_id,
            'status' => $this->quoteResponse->status->value,
            'expired_at' => $this->quoteResponse->expires_at?->toIso8601String(),
        ];
    }
}
