<?php

namespace App\Filament\Vendor\Resources\QuoteRequests\Schemas;

use App\Filament\Forms\Components\ExpiryPicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Request Settings')
                    ->schema([
                        Toggle::make('is_anonymous')
                            ->label('Anonymous Request')
                            ->helperText('Hide your identity from suppliers until you accept a quote')
                            ->default(false)
                            ->columnSpanFull(),

                        ...ExpiryPicker::make('expires_at', [
                            '1_hour' => '1 hour',
                            '4_hours' => '4 hours',
                            '24_hours' => '24 hours',
                            '3_days' => '3 days',
                            '7_days' => '7 days',
                            'custom' => 'Select from calendar...',
                        ]),
                    ]),
            ]);
    }
}
