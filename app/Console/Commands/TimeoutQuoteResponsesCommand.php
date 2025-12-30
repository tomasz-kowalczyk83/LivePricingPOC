<?php

namespace App\Console\Commands;

use App\Models\QuoteResponse;
use App\Services\OutboxEventService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimeoutQuoteResponsesCommand extends Command
{
    protected $signature = 'quotes:timeout-responses {--dry-run : Show what would be timed out without making changes}';

    protected $description = 'Mark expired quote responses as timeout and record outbox events';

    public function __construct(
        protected OutboxEventService $outboxEventService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $chunkSize = config('quotes.processing.chunk_size', 100);
        $timeoutCount = 0;

        $this->info('Scanning for timed out quote responses...');

        QuoteResponse::query()
            ->expirable()
            ->chunkById($chunkSize, function ($responses) use ($isDryRun, &$timeoutCount) {
                foreach ($responses as $response) {
                    if ($isDryRun) {
                        $this->line("Would timeout QuoteResponse #{$response->id} (expired at: {$response->expires_at})");
                        $timeoutCount++;

                        continue;
                    }

                    DB::transaction(function () use ($response) {
                        $response->markAsTimeout();
                        $this->outboxEventService->recordQuoteResponseTimeout($response);
                    });

                    $timeoutCount++;
                    Log::info("Timed out QuoteResponse #{$response->id}");
                }
            });

        $prefix = $isDryRun ? '[DRY RUN] Would timeout' : 'Timed out';
        $this->info("{$prefix} {$timeoutCount} quote response(s).");

        return self::SUCCESS;
    }
}
