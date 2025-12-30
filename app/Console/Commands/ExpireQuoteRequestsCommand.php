<?php

namespace App\Console\Commands;

use App\Models\QuoteRequest;
use App\Services\OutboxEventService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireQuoteRequestsCommand extends Command
{
    protected $signature = 'quotes:expire-requests {--dry-run : Show what would be expired without making changes}';

    protected $description = 'Mark expired quote requests as expired and record outbox events';

    public function __construct(
        protected OutboxEventService $outboxEventService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $chunkSize = config('quotes.processing.chunk_size', 100);
        $expiredCount = 0;

        $this->info('Scanning for expired quote requests...');

        QuoteRequest::query()
            ->expirable()
            ->chunkById($chunkSize, function ($requests) use ($isDryRun, &$expiredCount) {
                foreach ($requests as $request) {
                    if ($isDryRun) {
                        $this->line("Would expire QuoteRequest #{$request->id} (expired at: {$request->expires_at})");
                        $expiredCount++;

                        continue;
                    }

                    DB::transaction(function () use ($request) {
                        $request->markAsExpired();
                        $this->outboxEventService->recordQuoteRequestExpired($request);
                    });

                    $expiredCount++;
                    Log::info("Expired QuoteRequest #{$request->id}");
                }
            });

        $prefix = $isDryRun ? '[DRY RUN] Would expire' : 'Expired';
        $this->info("{$prefix} {$expiredCount} quote request(s).");

        return self::SUCCESS;
    }
}
