<?php

namespace App\Filament\Vendor\Resources\QuoteResponses\Pages;

use App\Filament\Vendor\Resources\QuoteResponses\QuoteResponseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuoteResponse extends ViewRecord
{
    protected static string $resource = QuoteResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
