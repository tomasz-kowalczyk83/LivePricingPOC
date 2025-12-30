<?php

namespace App\Filament\Vendor\Resources\QuoteRequests;

use App\Filament\Vendor\Resources\QuoteRequests\Pages\CreateQuoteRequest;
use App\Filament\Vendor\Resources\QuoteRequests\Pages\EditQuoteRequest;
use App\Filament\Vendor\Resources\QuoteRequests\Pages\ListQuoteRequests;
use App\Filament\Vendor\Resources\QuoteRequests\Pages\ViewQuoteRequest;
use App\Filament\Vendor\Resources\QuoteRequests\RelationManagers\ResponsesRelationManager;
use App\Filament\Vendor\Resources\QuoteRequests\Schemas\QuoteRequestForm;
use App\Filament\Vendor\Resources\QuoteRequests\Schemas\QuoteRequestInfolist;
use App\Filament\Vendor\Resources\QuoteRequests\Tables\QuoteRequestsTable;
use App\Models\QuoteRequest;
use App\Models\Trader;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'My Quote Requests';

    public static function shouldRegisterNavigation(): bool
    {
        $tenant = Filament::getTenant();

        return $tenant instanceof Trader && $tenant->isBuyer();
    }

    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();

        // Only show requests created by this buyer
        if ($tenant instanceof Trader && $tenant->isBuyer()) {
            return parent::getEloquentQuery()->where('trader_id', $tenant->id);
        }

        // Suppliers shouldn't see quote requests through this resource
        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return QuoteRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuoteRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuoteRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ResponsesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuoteRequests::route('/'),
            'create' => CreateQuoteRequest::route('/create'),
            'view' => ViewQuoteRequest::route('/{record}'),
            'edit' => EditQuoteRequest::route('/{record}/edit'),
        ];
    }
}
