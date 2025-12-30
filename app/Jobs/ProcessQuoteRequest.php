<?php

namespace App\Jobs;

use App\Enums\TraderTypeEnum;
use App\Models\QuoteRequest;
use App\Models\Trader;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessQuoteRequest implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public QuoteRequest $quoteRequest
    ) {}

    public function handle(): int
    {
        Log::info("Processing quote request {$this->quoteRequest->id}");

        $this->quoteRequest->markAsProcessing();

        // Get the buyer's company to exclude their own suppliers
        $buyerCompanyId = $this->quoteRequest->trader?->company_id;

        $suppliers = Trader::query()
            ->where('is_active', true)
            ->where('type', TraderTypeEnum::SUPPLIER)
            ->when($buyerCompanyId, function ($query) use ($buyerCompanyId) {
                // Exclude suppliers from the same company as the buyer
                $query->where('company_id', '!=', $buyerCompanyId);
            })
            ->get();

        Log::info("Found {$suppliers->count()} eligible suppliers for quote request {$this->quoteRequest->id}".
            ($buyerCompanyId ? " (excluded company_id: {$buyerCompanyId})" : ''));

        if ($suppliers->isEmpty()) {
            Log::warning("No eligible suppliers found for quote request {$this->quoteRequest->id}");
            $this->quoteRequest->markAsCompleted();

            return 0;
        }

        // Set the expected responses count
        $this->quoteRequest->update([
            'expected_responses_count' => $suppliers->count(),
        ]);

        Log::debug("Expected responses count set to {$this->quoteRequest->expected_responses_count}");

        foreach ($suppliers as $supplier) {
            ProcessSupplierQuote::dispatch($this->quoteRequest, $supplier);
        }

        return 1;
    }
}
