<?php

namespace App\Filament\Vendor\Resources\QuoteResponses\Pages;

use App\Actions\QuoteResponse\DeclineQuoteAction;
use App\Actions\QuoteResponse\SubmitQuoteAction;
use App\Enums\QuoteResponseStatusEnum;
use App\Filament\Vendor\Resources\QuoteResponses\QuoteResponseResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQuoteResponse extends EditRecord
{
    protected static string $resource = QuoteResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit')
                ->label('Submit Quote')
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn () => $this->record->status === QuoteResponseStatusEnum::PENDING)
                ->disabled(function (): bool {
                    $data = $this->form->getRawState();

                    return empty($data['quoted_price']);
                })
                ->before(function (Action $action) {
                    // Validate form - this runs after confirmation but provides safety
                    $this->form->validate();

                    $data = $this->form->getState();
                    if (empty($data['quoted_price'])) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot submit')
                            ->body('Please enter a quoted price before submitting.')
                            ->send();

                        $action->cancel();
                    }
                })
                ->action(function () {
                    // Save form changes
                    $this->save(false);

                    // Execute the action
                    app(SubmitQuoteAction::class)->execute($this->record);

                    Notification::make()
                        ->success()
                        ->title('Quote submitted')
                        ->body('Your quote has been submitted to the buyer.')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->requiresConfirmation()
                ->modalHeading('Submit Quote')
                ->modalDescription('Are you sure you want to submit this quote? The buyer will be able to see your quoted price and stock availability.')
                ->modalSubmitActionLabel('Yes, submit quote'),

            Action::make('decline')
                ->label('Decline')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Decline Quote Request')
                ->modalDescription('Are you sure you want to decline this quote request? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, decline')
                ->visible(fn () => $this->record->status === QuoteResponseStatusEnum::PENDING)
                ->action(function () {
                    app(DeclineQuoteAction::class)->execute($this->record);

                    Notification::make()
                        ->warning()
                        ->title('Quote declined')
                        ->body('You have declined this quote request.')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            ViewAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        // Remove the default Save button since Submit Quote replaces it
        return [];
    }
}
