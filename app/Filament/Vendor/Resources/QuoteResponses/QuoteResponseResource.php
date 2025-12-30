<?php

namespace App\Filament\Vendor\Resources\QuoteResponses;

use App\Filament\Vendor\Resources\QuoteResponses\Pages\EditQuoteResponse;
use App\Filament\Vendor\Resources\QuoteResponses\Pages\ListQuoteResponses;
use App\Filament\Vendor\Resources\QuoteResponses\Pages\ViewQuoteResponse;
use App\Filament\Vendor\Resources\QuoteResponses\Schemas\QuoteResponseForm;
use App\Filament\Vendor\Resources\QuoteResponses\Schemas\QuoteResponseInfolist;
use App\Filament\Vendor\Resources\QuoteResponses\Tables\QuoteResponsesTable;
use App\Models\QuoteResponse;
use App\Models\Trader;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteResponseResource extends Resource
{
    protected static ?string $model = QuoteResponse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'My Quote Responses';

    public static function shouldRegisterNavigation(): bool
    {
        $tenant = Filament::getTenant();

        return $tenant instanceof Trader && $tenant->isSupplier();
    }

    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();

        // Only show responses assigned to this supplier
        if ($tenant instanceof Trader && $tenant->isSupplier()) {
            return parent::getEloquentQuery()->where('trader_id', $tenant->id);
        }

        // Buyers shouldn't see quote responses through this resource
        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return QuoteResponseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuoteResponseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuoteResponsesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuoteResponses::route('/'),
            'view' => ViewQuoteResponse::route('/{record}'),
            'edit' => EditQuoteResponse::route('/{record}/edit'),
        ];
    }
}
