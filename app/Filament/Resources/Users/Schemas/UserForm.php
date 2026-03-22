<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserType;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(table: User::class, column: 'email', ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->nullable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->confirmed()
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')
                    ->label('Confirm password')
                    ->password()
                    ->revealable()
                    ->dehydrated(false)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->requiredWith('password'),
                TextInput::make('linkedin_url')
                    ->label('LinkedIn URL')
                    ->url()
                    ->maxLength(500)
                    ->nullable(),
                Select::make('user_type')
                    ->options(UserType::class)
                    ->default(UserType::DEVELOPER->value)
                    ->required(),
                Select::make('roles')
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('guard_name', 'web')->orderBy('name'),
                    )
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Roles'),
                Toggle::make('can_access_admin_panel')
                    ->label('Can access admin panel')
                    ->default(false),
                DateTimePicker::make('email_verified_at')
                    ->label('Email verified at')
                    ->seconds(false)
                    ->nullable(),
            ]);
    }
}
