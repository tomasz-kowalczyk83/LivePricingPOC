<?php

namespace App\Filament\Vendor\Resources\QuoteResponses\Tables;

use App\Actions\QuoteResponse\DeclineQuoteAction;
use App\Actions\QuoteResponse\SubmitQuoteAction;
use App\Enums\QuoteResponseStatusEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuoteResponsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('quoteRequest.id')
                    ->label('Request #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('buyer_display')
                    ->label('Buyer')
                    ->state(fn ($record) => $record->quoteRequest?->getBuyerDisplayName() ?? '-')
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('quoteRequest.trader', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                    }),
                TextColumn::make('quoted_price')
                    ->money()
                    ->sortable()
                    ->placeholder('Not quoted'),
                TextColumn::make('stock_available')
                    ->numeric()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => $state->color())
                    ->formatStateUsing(fn ($state) => $state->label()),
                TextColumn::make('quoteRequest.expires_at')
                    ->label('Request Expires')
                    ->since()
                    ->sortable()
                    ->description(fn ($record) => $record->quoteRequest?->isExpired() ? 'Expired' : null)
                    ->color(fn ($record) => $record->quoteRequest?->isExpired() ? 'danger' : null)
                    ->tooltip(fn ($record) => $record->quoteRequest?->expires_at?->format('M j, Y g:i A')),
                TextColumn::make('expires_at')
                    ->label('Quote Expires')
                    ->since()
                    ->sortable()
                    ->description(fn ($record) => $record->isExpired() ? 'Expired' : null)
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : null)
                    ->tooltip(fn ($record) => $record->expires_at?->format('M j, Y g:i A')),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->created_at?->format('M j, Y g:i A')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(QuoteResponseStatusEnum::class),
            ])
            ->recordActions([
                Action::make('submit')
                    ->label('Submit')
                    ->color('success')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === QuoteResponseStatusEnum::PENDING && ! empty($record->quoted_price))
                    ->action(function ($record) {
                        app(SubmitQuoteAction::class)->execute($record);
                        Notification::make()
                            ->success()
                            ->title('Quote submitted')
                            ->send();
                    }),
                Action::make('decline')
                    ->label('Decline')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === QuoteResponseStatusEnum::PENDING)
                    ->action(function ($record) {
                        app(DeclineQuoteAction::class)->execute($record);
                        Notification::make()
                            ->warning()
                            ->title('Quote declined')
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make()
                    ->label('Enter Quote')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn ($record) => $record->status === QuoteResponseStatusEnum::PENDING),
            ]);
    }
}
