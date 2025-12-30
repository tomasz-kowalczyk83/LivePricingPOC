<?php

namespace App\Actions\QuoteResponse;

use App\Enums\QuoteResponseStatusEnum;
use App\Events\QuoteResponseDeclined;
use App\Models\QuoteResponse;
use Illuminate\Support\Facades\DB;

class DeclineQuoteAction
{
    public function execute(QuoteResponse $quoteResponse): void
    {
        DB::transaction(function () use ($quoteResponse) {
            $quoteResponse->update([
                'status' => QuoteResponseStatusEnum::DECLINED,
            ]);

            $quoteResponse->quoteRequest()->increment('responses_count');

            QuoteResponseDeclined::dispatch($quoteResponse);
        });
    }
}
