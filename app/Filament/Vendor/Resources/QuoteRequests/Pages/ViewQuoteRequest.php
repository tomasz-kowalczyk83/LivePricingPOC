<?php

namespace App\Filament\Vendor\Resources\QuoteRequests\Pages;

use App\Filament\Vendor\Resources\QuoteRequests\QuoteRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuoteRequest extends ViewRecord
{
    protected static string $resource = QuoteRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
