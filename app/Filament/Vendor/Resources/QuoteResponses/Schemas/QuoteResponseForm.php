<?php

namespace App\Filament\Vendor\Resources\QuoteResponses\Schemas;

use App\Filament\Forms\Components\ExpiryPicker;
use App\Models\QuoteResponse;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class QuoteResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quote Request Details')
                    ->description('Details of the quote request you are responding to')
                    ->schema([
                        Placeholder::make('buyer_name')
                            ->label('Buyer')
                            ->content(function (?Model $record): string {
                                if (! $record instanceof QuoteResponse) {
                                    return '-';
                                }

                                return $record->quoteRequest?->getBuyerDisplayName() ?? '-';
                            }),
                        Placeholder::make('request_created')
                            ->label('Requested')
                            ->content(function (?Model $record): string {
                                if (! $record instanceof QuoteResponse) {
                                    return '-';
                                }

                                return $record->quoteRequest?->created_at?->diffForHumans() ?? '-';
                            }),
                        Placeholder::make('request_expires')
                            ->label('Request Expires')
                            ->content(function (?Model $record): string {
                                if (! $record instanceof QuoteResponse) {
                                    return '-';
                                }

                                return $record->quoteRequest?->expires_at?->diffForHumans() ?? '-';
                            }),
                        Placeholder::make('request_status')
                            ->label('Request Status')
                            ->content(function (?Model $record): string {
                                if (! $record instanceof QuoteResponse) {
                                    return '-';
                                }

                                return $record->quoteRequest?->status?->label() ?? '-';
                            }),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Your Quote')
                    ->description('Enter your quote details and set validity period')
                    ->schema([
                        TextInput::make('quoted_price')
                            ->label('Quoted Price')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('$')
                            ->live()
                            ->helperText('Enter your quoted price for this request'),
                        TextInput::make('stock_available')
                            ->label('Stock Available')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Optional: Number of units available'),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->helperText('Optional: Additional notes for the buyer')
                            ->columnSpanFull(),
                        ...ExpiryPicker::make('expires_at', [
                            '15_minutes' => '15 minutes',
                            '30_minutes' => '30 minutes',
                            '1_hour' => '1 hour',
                            '4_hours' => '4 hours',
                            '24_hours' => '24 hours',
                            'custom' => 'Select from calendar...',
                        ]),
                    ])
                    ->columns(2),
            ]);
    }
}
