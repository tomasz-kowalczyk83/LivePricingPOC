<?php

namespace App\Filament\Vendor\Resources\QuoteRequests\RelationManagers;

use App\Actions\QuoteResponse\AcceptQuoteAction;
use App\Enums\QuoteRequestStatusEnum;
use App\Enums\QuoteResponseStatusEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'Quote Responses';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return true;
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Supplier Details')
                    ->schema([
                        TextEntry::make('trader.name')
                            ->label('Supplier'),
                        TextEntry::make('trader.company.name')
                            ->label('Company'),
                    ])
                    ->columns(2),

                Section::make('Quote Details')
                    ->schema([
                        TextEntry::make('quoted_price')
                            ->money()
                            ->placeholder('Not quoted'),
                        TextEntry::make('stock_available')
                            ->numeric()
                            ->placeholder('Not specified'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => $state->color())
                            ->formatStateUsing(fn ($state) => $state->label()),
                        TextEntry::make('expires_at')
                            ->label('Quote Expires')
                            ->since()
                            ->color(fn ($record) => $record->isExpired() ? 'danger' : null),
                        TextEntry::make('notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Received')
                            ->since(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->since(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes())
            ->recordTitleAttribute('id')
            ->defaultSort('quoted_price', 'asc')
            ->columns([
                TextColumn::make('trader.name')
                    ->label('Supplier')
                    ->searchable(),
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
                TextColumn::make('expires_at')
                    ->label('Expires')
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
                Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Accept Quote')
                    ->modalDescription('Are you sure you want to accept this quote? This will reject all other responses and complete the quote request.')
                    ->modalSubmitActionLabel('Yes, accept quote')
                    ->visible(function ($record) {
                        // Only show for submitted quotes that haven't expired
                        // and when the parent request is still active
                        return $record->status === QuoteResponseStatusEnum::SUBMITTED
                            && ! $record->isExpired()
                            && in_array($this->getOwnerRecord()->status, [
                                QuoteRequestStatusEnum::PENDING,
                                QuoteRequestStatusEnum::PROCESSING,
                            ], true);
                    })
                    ->action(function ($record) {
                        app(AcceptQuoteAction::class)->execute($record);

                        Notification::make()
                            ->success()
                            ->title('Quote accepted')
                            ->body('The quote has been accepted and the request is now complete.')
                            ->send();
                    }),
                ViewAction::make(),
            ]);
    }
}
