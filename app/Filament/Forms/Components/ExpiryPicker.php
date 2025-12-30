<?php

namespace App\Filament\Forms\Components;

use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get as SchemaGet;
use Filament\Schemas\Components\Utilities\Set as SchemaSet;

class ExpiryPicker
{
    public static function make(string $field = 'expires_at', array $presets = []): array
    {
        $defaultPresets = [
            '15_minutes' => '15 minutes',
            '1_hour' => '1 hour',
            '4_hours' => '4 hours',
            '24_hours' => '24 hours',
            '3_days' => '3 days',
            '7_days' => '7 days',
            'custom' => 'Select from calendar...',
        ];

        $presets = empty($presets) ? $defaultPresets : $presets;

        return [
            Select::make("{$field}_preset")
                ->label('Expiry')
                ->options($presets)
                ->default('24_hours')
                ->live()
                ->afterStateUpdated(function (SchemaSet $set, ?string $state) use ($field) {
                    if ($state === 'custom' || $state === null) {
                        return;
                    }

                    $expiry = self::calculateExpiry($state);
                    $set($field, $expiry?->format('Y-m-d H:i:s'));
                })
                ->dehydrated(false)
                ->columnSpanFull(),

            DateTimePicker::make($field)
                ->label('Custom Expiry Date & Time')
                ->native(false)
                ->minDate(now())
                ->visible(fn (SchemaGet $get) => $get("{$field}_preset") === 'custom')
                ->required(fn (SchemaGet $get) => $get("{$field}_preset") === 'custom')
                ->columnSpanFull(),
        ];
    }

    public static function calculateExpiry(?string $preset): ?Carbon
    {
        return match ($preset) {
            '15_minutes' => now()->addMinutes(15),
            '30_minutes' => now()->addMinutes(30),
            '1_hour' => now()->addHour(),
            '2_hours' => now()->addHours(2),
            '4_hours' => now()->addHours(4),
            '8_hours' => now()->addHours(8),
            '12_hours' => now()->addHours(12),
            '24_hours' => now()->addDay(),
            '2_days' => now()->addDays(2),
            '3_days' => now()->addDays(3),
            '7_days' => now()->addDays(7),
            '14_days' => now()->addDays(14),
            '30_days' => now()->addDays(30),
            default => null,
        };
    }
}
