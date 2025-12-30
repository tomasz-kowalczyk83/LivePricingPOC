<?php

namespace App\Jobs;

use App\Enums\QuoteResponseStatusEnum;
use App\Events\QuoteResponseReceived;
use App\Models\QuoteRequest;
use App\Models\QuoteResponse;
use App\Models\Supplier;
use App\Models\Trader;
use App\Services\SupplierIntegrations\SupplierIntegrationFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessSupplierQuote implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public QuoteRequest $quoteRequest,
        public Trader $supplier
    ) {}

    public function handle(SupplierIntegrationFactory $factory): void
    {
        Log::info("Processing supplier quote for supplier {$this->supplier->id} on request {$this->quoteRequest->id}");

        //        $integration = $factory->make($this->supplier);
        //        $result = $integration->requestQuote($this->supplier, $this->quoteRequest);

        //        if ($result === null) {
        //            Log::warning("No quote received from supplier {$this->supplier->id}");
        //            return;
        //        }

        $quoteResponse = QuoteResponse::create([
            'quote_request_id' => $this->quoteRequest->id,
            'trader_id' => $this->supplier->id,
            'quoted_price' => null,
            'stock_available' => null,
            'response_time_seconds' => null,
            'notes' => 'Manual quote request sent. Awaiting supplier response.',
            'status' => QuoteResponseStatusEnum::PENDING,
        ]);

        //        $this->quoteRequest->incrementResponsesCount();

        QuoteResponseReceived::dispatch($quoteResponse);

        Log::info("Quote response saved for supplier {$this->supplier->id}");
    }
}
