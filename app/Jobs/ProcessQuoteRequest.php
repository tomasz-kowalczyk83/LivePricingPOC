<?php

namespace App\Jobs;

use App\Models\QuoteRequest;
use App\Models\Supplier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessQuoteRequest implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public QuoteRequest $quoteRequest
    ) {
    }

    public function handle(): void
    {
        Log::info("Processing quote request {$this->quoteRequest->id}");

        $this->quoteRequest->markAsProcessing();

        $suppliers = Supplier::where('is_active', true)->get();

        foreach ($suppliers as $supplier) {
            ProcessSupplierQuote::dispatch($this->quoteRequest, $supplier);
        }
    }
}
