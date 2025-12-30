<?php

namespace App\Jobs;

use App\Events\QuoteRequestExpired;
use App\Events\QuoteResponseTimedOut;
use App\Models\OutboxEvent;
use App\Services\OutboxEventService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessOutboxEvents implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        OutboxEvent::pending()
            ->oldest()
            ->limit(100)
            ->each(function (OutboxEvent $event) {
                $this->processEvent($event);
            });
    }

    protected function processEvent(OutboxEvent $event): void
    {
        try {
            match ($event->event_type) {
                OutboxEventService::EVENT_QUOTE_REQUEST_EXPIRED => $this->handleQuoteRequestExpired($event),
                OutboxEventService::EVENT_QUOTE_RESPONSE_TIMEOUT => $this->handleQuoteResponseTimeout($event),
                default => Log::warning("Unknown outbox event type: {$event->event_type}"),
            };

            $event->markAsProcessed();
        } catch (\Throwable $e) {
            Log::error("Failed to process outbox event {$event->id}: {$e->getMessage()}");
        }
    }

    protected function handleQuoteRequestExpired(OutboxEvent $event): void
    {
        if ($event->eventable) {
            QuoteRequestExpired::dispatch($event->eventable);
        }
    }

    protected function handleQuoteResponseTimeout(OutboxEvent $event): void
    {
        if ($event->eventable) {
            QuoteResponseTimedOut::dispatch($event->eventable);
        }
    }
}
