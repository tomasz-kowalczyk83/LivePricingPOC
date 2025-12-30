<?php

namespace App\Actions\QuoteResponse;

use App\Enums\QuoteRequestStatusEnum;
use App\Enums\QuoteResponseStatusEnum;
use App\Models\QuoteResponse;
use Illuminate\Support\Facades\DB;

class AcceptQuoteAction
{
    public function execute(QuoteResponse $quoteResponse): void
    {
        DB::transaction(function () use ($quoteResponse) {
            // Accept this response
            $quoteResponse->update([
                'status' => QuoteResponseStatusEnum::ACCEPTED,
            ]);

            // Reject all other submitted responses for this quote request
            $quoteResponse->quoteRequest->responses()
                ->where('id', '!=', $quoteResponse->id)
                ->where('status', QuoteResponseStatusEnum::SUBMITTED)
                ->update(['status' => QuoteResponseStatusEnum::REJECTED]);

            // Mark the quote request as completed
            $quoteResponse->quoteRequest->update([
                'status' => QuoteRequestStatusEnum::COMPLETED,
            ]);
        });
    }
}
