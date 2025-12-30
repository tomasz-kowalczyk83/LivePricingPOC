<?php

namespace App\Console\Commands;

use App\Jobs\ProcessOutboxEvents;
use Illuminate\Console\Command;

class ProcessOutboxCommand extends Command
{
    protected $signature = 'outbox:process';

    protected $description = 'Dispatch job to process pending outbox events';

    public function handle(): int
    {
        ProcessOutboxEvents::dispatch();

        $this->info('Dispatched outbox processing job.');

        return self::SUCCESS;
    }
}
