<?php

namespace App\Events;

use App\Models\QuoteRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteRequestExpired implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public QuoteRequest $quoteRequest
    ) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('quote-request.'.$this->quoteRequest->id),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->quoteRequest->id,
            'status' => $this->quoteRequest->status->value,
            'expired_at' => $this->quoteRequest->expires_at?->toIso8601String(),
        ];
    }
}
