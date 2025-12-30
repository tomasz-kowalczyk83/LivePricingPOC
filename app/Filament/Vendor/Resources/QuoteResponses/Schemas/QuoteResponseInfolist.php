<?php

namespace App\Filament\Vendor\Resources\QuoteResponses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteResponseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quote Request')
                    ->schema([
                        TextEntry::make('quoteRequest.id')
                            ->label('Request #'),
                        TextEntry::make('buyer_display')
                            ->label('Buyer')
                            ->state(fn ($record) => $record->quoteRequest?->getBuyerDisplayName() ?? '-'),
                        TextEntry::make('quoteRequest.status')
                            ->label('Request Status')
                            ->badge()
                            ->color(fn ($state) => $state?->color())
                            ->formatStateUsing(fn ($state) => $state?->label()),
                        TextEntry::make('quoteRequest.created_at')
                            ->label('Requested')
                            ->since(),
                        TextEntry::make('quoteRequest.expires_at')
                            ->label('Request Expires')
                            ->since()
                            ->color(fn ($record) => $record->quoteRequest?->isExpired() ? 'danger' : null),
                    ])
                    ->columns(5),

                Section::make('Your Response')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => $state->color())
                            ->formatStateUsing(fn ($state) => $state->label()),
                        TextEntry::make('quoted_price')
                            ->money()
                            ->placeholder('Not quoted'),
                        TextEntry::make('stock_available')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('expires_at')
                            ->label('Expires')
                            ->since()
                            ->placeholder('No expiry'),
                        TextEntry::make('notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Received')
                            ->since(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->since(),
                    ])
                    ->columns(3),
            ]);
    }
}
