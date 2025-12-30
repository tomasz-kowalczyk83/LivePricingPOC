<?php

namespace App\Filament\Vendor\Resources\QuoteRequests\Pages;

use App\Filament\Vendor\Resources\QuoteRequests\QuoteRequestResource;
use App\Jobs\ProcessQuoteRequest;
use Filament\Resources\Pages\CreateRecord;

class CreateQuoteRequest extends CreateRecord
{
    protected static string $resource = QuoteRequestResource::class;

    public function afterCreate(): void
    {
        ProcessQuoteRequest::dispatch($this->record);
    }
}
