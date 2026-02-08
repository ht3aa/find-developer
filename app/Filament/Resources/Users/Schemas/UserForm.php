<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('linkedin_url')
                            ->label('LinkedIn URL')
                            ->url()
                            ->nullable()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-link')
                            ->helperText('Enter the full LinkedIn profile URL (e.g., https://linkedin.com/in/username)'),

                        Select::make('user_type')
                            ->label('User Type')
                            ->options(UserType::class)
                            ->default(UserType::DEVELOPER)
                            ->required()
                            ->searchable(),

                        TextInput::make('password')
                            ->password()
                            ->rules([Password::default()])
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                            ->visible(fn (string $operation): bool => $operation === 'create'),

                        Toggle::make('can_access_admin_panel')
                            ->label('Can Access Admin Panel')
                            ->default(false)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
