<?php

namespace App\Filament\Vendor\Resources\QuoteResponses\Pages;

use App\Filament\Vendor\Resources\QuoteResponses\QuoteResponseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuoteResponses extends ListRecords
{
    protected static string $resource = QuoteResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
