<?php

namespace App\Actions\QuoteResponse;

use App\Enums\QuoteResponseStatusEnum;
use App\Events\QuoteResponseSubmitted;
use App\Models\QuoteResponse;
use Illuminate\Support\Facades\DB;

class SubmitQuoteAction
{
    public function execute(QuoteResponse $quoteResponse): void
    {
        DB::transaction(function () use ($quoteResponse) {
            $quoteResponse->update([
                'status' => QuoteResponseStatusEnum::SUBMITTED,
            ]);

            $quoteResponse->quoteRequest()->increment('responses_count');

            QuoteResponseSubmitted::dispatch($quoteResponse);
        });
    }
}
