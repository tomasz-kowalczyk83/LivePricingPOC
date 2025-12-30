<?php

namespace App\Filament\Vendor\Pages;

use App\Models\Trader;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TraderSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected string $view = 'filament.vendor.pages.trader-settings';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public function mount(): void
    {
        $trader = Filament::getTenant();

        if ($trader instanceof Trader) {
            $this->form->fill([
                'default_request_expiry_minutes' => $trader->default_request_expiry_minutes,
                'default_response_expiry_minutes' => $trader->default_response_expiry_minutes,
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        $trader = Filament::getTenant();

        return $schema
            ->statePath('data')
            ->components([
                Section::make('Default Expiry Settings')
                    ->description('Configure default expiry times for your quote requests and responses. These values will be used when creating new quotes if no custom expiry is selected.')
                    ->schema([
                        TextInput::make('default_request_expiry_minutes')
                            ->label('Default Request Expiry (minutes)')
                            ->helperText('How long your quote requests remain open for responses. Leave empty to use system default.')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(43200) // 30 days
                            ->placeholder(config('quotes.request.default_expiry_minutes'))
                            ->suffix('minutes')
                            ->visible(fn () => $trader instanceof Trader && $trader->isBuyer()),
                        TextInput::make('default_response_expiry_minutes')
                            ->label('Default Response Expiry (minutes)')
                            ->helperText('How long your quote responses remain valid. Leave empty to use system default.')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(43200) // 30 days
                            ->placeholder(config('quotes.response.default_expiry_minutes'))
                            ->suffix('minutes')
                            ->visible(fn () => $trader instanceof Trader && $trader->isSupplier()),
                    ])
                    ->columns(1),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $trader = Filament::getTenant();

        if (! $trader instanceof Trader) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Could not save settings.')
                ->send();

            return;
        }

        $trader->update([
            'default_request_expiry_minutes' => $data['default_request_expiry_minutes'] ?: null,
            'default_response_expiry_minutes' => $data['default_response_expiry_minutes'] ?: null,
        ]);

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->body('Your default expiry settings have been updated.')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }
}
