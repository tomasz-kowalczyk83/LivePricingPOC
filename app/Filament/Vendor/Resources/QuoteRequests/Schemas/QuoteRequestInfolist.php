<?php

namespace App\Filament\Vendor\Resources\QuoteRequests\Schemas;

use App\Models\QuoteRequest;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Request Details')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Request #'),
                        TextEntry::make('buyer_display')
                            ->label('Buyer')
                            ->state(fn (QuoteRequest $record) => $record->getBuyerDisplayName()),
                        IconEntry::make('is_anonymous')
                            ->label('Anonymous')
                            ->boolean()
                            ->trueIcon('heroicon-o-eye-slash')
                            ->falseIcon('heroicon-o-eye')
                            ->trueColor('warning')
                            ->falseColor('success'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => $state->color())
                            ->formatStateUsing(fn ($state) => $state->label()),
                        TextEntry::make('progress')
                            ->label('Responses')
                            ->state(function (QuoteRequest $record): string {
                                $expected = $record->expected_responses_count;
                                $responded = $record->responses_count;

                                if ($expected === 0) {
                                    return 'Pending';
                                }

                                return "{$responded} of {$expected} responded";
                            }),
                        TextEntry::make('expires_at')
                            ->label('Expires')
                            ->since()
                            ->placeholder('No expiry'),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->since(),
                    ])
                    ->columns(4),
            ]);
    }
}
