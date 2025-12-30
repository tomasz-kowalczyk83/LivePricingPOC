<?php

namespace App\Filament\Pages\Auth;

use App\Enums\TraderTypeEnum;
use App\Models\User;
use BackedEnum;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getCompanyNameFormComponent(),
                $this->getBusinessTypeFormComponent(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    private function getCompanyNameFormComponent(): Component
    {
        return TextInput::make('company_name')
            ->label('Company Name')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    private function getBusinessTypeFormComponent(): Component
    {
        return Select::make('business_type')
            ->options(
                collect(TraderTypeEnum::cases())
                    ->mapWithKeys(fn (BackedEnum $case) => [Str::lower($case->name) => Str::ucfirst($case->value)])
            )
            ->required()
            ->label('Business Type');
    }

    protected function handleRegistration(array $data): Model
    {
        return tap($this->getUserModel()::create($data), function (User $user) use ($data) {
            $company = $user->companies()->create([
                'name' => $data['company_name'],
                'type' => $data['business_type'],
            ]);

            $company->traders()->create([
                'name' => Str::kebab($data['company_name'].$data['business_type']),
                'type' => $data['business_type'],
            ]);
        });
    }
}
