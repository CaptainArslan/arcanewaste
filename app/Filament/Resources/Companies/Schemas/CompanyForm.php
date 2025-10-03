<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('customer_panel_url')
                    ->default(null),
                TextInput::make('logo')
                    ->default(null),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('website')
                    ->default(null),
                TextInput::make('onboarding_status')
                    ->required()
                    ->default('pending'),
                TextInput::make('finix_identity_id')
                    ->default(null),
                TextInput::make('finix_merchant_id')
                    ->default(null),
                TextInput::make('finix_onboarding_form_id')
                    ->default(null),
                Textarea::make('finix_onboarding_url')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('finix_onboarding_url_expired_at'),
                TextInput::make('finix_onboarding_status')
                    ->default(null),
                Textarea::make('finix_onboarding_notes')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('finix_onboarding_completed_at'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
