<?php

namespace App\Filament\Vendor\Resources\QuoteRequests\Tables;

use App\Enums\QuoteRequestStatusEnum;
use App\Models\QuoteRequest;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuoteRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Request #')
                    ->sortable(),
                TextColumn::make('buyer_display')
                    ->label('Buyer')
                    ->state(fn (QuoteRequest $record) => $record->getBuyerDisplayName())
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('trader', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                    }),
                IconColumn::make('is_anonymous')
                    ->label('Anon')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye-slash')
                    ->falseIcon('heroicon-o-eye')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => $state->color())
                    ->formatStateUsing(fn ($state) => $state->label()),
                TextColumn::make('progress')
                    ->label('Responses')
                    ->state(function (QuoteRequest $record): string {
                        $expected = $record->expected_responses_count;
                        $responded = $record->responses_count;

                        if ($expected === 0) {
                            return '-';
                        }

                        return "{$responded}/{$expected}";
                    })
                    ->description(function (QuoteRequest $record) {
                        if ($record->expected_responses_count === 0) {
                            return 'pending';
                        }

                        return $record->responses_count >= $record->expected_responses_count ? 'complete' : 'awaiting';
                    }),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->since()
                    ->sortable()
                    ->description(fn ($record) => $record->isExpired() ? 'Expired' : null)
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : null)
                    ->tooltip(fn ($record) => $record->expires_at?->format('M j, Y g:i A')),
                TextColumn::make('created_at')
                    ->label('Requested')
                    ->since()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->created_at?->format('M j, Y g:i A')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(QuoteRequestStatusEnum::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
