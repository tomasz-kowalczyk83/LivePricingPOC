<?php

namespace App\Services;

use App\Models\OutboxEvent;
use App\Models\QuoteRequest;
use App\Models\QuoteResponse;
use Illuminate\Database\Eloquent\Model;

class OutboxEventService
{
    public const EVENT_QUOTE_REQUEST_EXPIRED = 'quote_request.expired';

    public const EVENT_QUOTE_RESPONSE_TIMEOUT = 'quote_response.timeout';

    public function record(string $eventType, Model $model, array $payload = []): OutboxEvent
    {
        return OutboxEvent::create([
            'event_type' => $eventType,
            'eventable_type' => $model->getMorphClass(),
            'eventable_id' => $model->getKey(),
            'payload' => array_merge([
                'model_id' => $model->getKey(),
                'model_type' => $model->getMorphClass(),
                'occurred_at' => now()->toIso8601String(),
            ], $payload),
        ]);
    }

    public function recordQuoteRequestExpired(QuoteRequest $quoteRequest): OutboxEvent
    {
        return $this->record(
            self::EVENT_QUOTE_REQUEST_EXPIRED,
            $quoteRequest,
            [
                'trader_id' => $quoteRequest->trader_id,
                'status_before' => $quoteRequest->getOriginal('status')?->value ?? $quoteRequest->status->value,
                'expired_at' => $quoteRequest->expires_at?->toIso8601String(),
            ]
        );
    }

    public function recordQuoteResponseTimeout(QuoteResponse $quoteResponse): OutboxEvent
    {
        return $this->record(
            self::EVENT_QUOTE_RESPONSE_TIMEOUT,
            $quoteResponse,
            [
                'quote_request_id' => $quoteResponse->quote_request_id,
                'trader_id' => $quoteResponse->trader_id,
                'status_before' => $quoteResponse->getOriginal('status')?->value ?? $quoteResponse->status->value,
                'expired_at' => $quoteResponse->expires_at?->toIso8601String(),
            ]
        );
    }
}
