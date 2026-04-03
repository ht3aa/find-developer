<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('linkedin_url')
                    ->url()
                    ->default(null),
                Select::make('user_type')
                    ->options(UserType::class)
                    ->default('developer')
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (mixed $livewire): bool => $livewire instanceof CreateRecord),
                Toggle::make('can_access_admin_panel')
                    ->required(),
            ]);
    }
}
