<?php

namespace App\Jobs;

use App\Events\QuoteResponseReceived;
use App\Models\QuoteRequest;
use App\Models\QuoteResponse;
use App\Models\Supplier;
use App\Services\SupplierIntegrations\SupplierIntegrationFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessSupplierQuote implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public QuoteRequest $quoteRequest,
        public Supplier $supplier
    ) {
    }

    public function handle(SupplierIntegrationFactory $factory): void
    {
        Log::info("Processing supplier quote for supplier {$this->supplier->id} on request {$this->quoteRequest->id}");

        $integration = $factory->make($this->supplier);
        $result = $integration->requestQuote($this->supplier, $this->quoteRequest);

        if ($result === null) {
            Log::warning("No quote received from supplier {$this->supplier->id}");
            return;
        }

        $quoteResponse = QuoteResponse::create([
            'quote_request_id' => $this->quoteRequest->id,
            'supplier_id' => $this->supplier->id,
            ...$result,
        ]);

        $this->quoteRequest->incrementResponsesCount();

        QuoteResponseReceived::dispatch($quoteResponse);

        Log::info("Quote response saved for supplier {$this->supplier->id}");
    }
}
