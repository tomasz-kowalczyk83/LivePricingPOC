<?php

namespace App\Events;

use App\Models\QuoteResponse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteResponseDeclined
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public QuoteResponse $quoteResponse
    ) {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('quote-request.'.$this->quoteResponse->quote_request_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->quoteResponse->id,
            'status' => $this->quoteResponse->status,
        ];
    }
}
