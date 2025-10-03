<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('customer_panel_url'),
                TextEntry::make('logo'),
                TextEntry::make('phone'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('website'),
                TextEntry::make('onboarding_status'),
                TextEntry::make('finix_identity_id'),
                TextEntry::make('finix_merchant_id'),
                TextEntry::make('finix_onboarding_form_id'),
                TextEntry::make('finix_onboarding_url_expired_at')
                    ->dateTime(),
                TextEntry::make('finix_onboarding_status'),
                TextEntry::make('finix_onboarding_completed_at')
                    ->dateTime(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->dateTime(),
            ]);
    }
}
