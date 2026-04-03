<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use App\Services\GiteaService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('linkedin_url')
                    ->searchable(),
                TextColumn::make('user_type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('can_access_admin_panel')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('createInGitea')
                    ->label('Create in Gitea')
                    ->icon(Heroicon::OutlinedUserPlus)
                    ->visible(fn (): bool => app(GiteaService::class)->isConfigured())
                    ->authorize('createInGitea')
                    ->modalHeading('Create Gitea user')
                    ->modalDescription('Creates an account on your Gitea server for this user using the admin API. Use an access token with admin permission.')
                    ->fillForm(function (User $record): array {
                        return [
                            'username' => app(GiteaService::class)->suggestedUsernameFromEmail($record->email),
                            'must_change_password' => true,
                        ];
                    })
                    ->schema([
                        TextInput::make('username')
                            ->label('Gitea username')
                            ->required()
                            ->maxLength(40)
                            ->rule('regex:/^[a-zA-Z0-9._-]+$/')
                            ->helperText('Letters, numbers, dots, hyphens, and underscores only.'),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->confirmed(),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->required()
                            ->label('Confirm password'),
                        Toggle::make('must_change_password')
                            ->label('Require password change on first login')
                            ->default(true),
                    ])
                    ->action(function (array $data, User $record): void {
                        try {
                            app(GiteaService::class)->createUser(
                                username: $data['username'],
                                email: $record->email,
                                password: $data['password'],
                                fullName: $record->name,
                                mustChangePassword: (bool) ($data['must_change_password'] ?? true),
                            );
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Could not create Gitea user')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            throw new Halt;
                        }
                    })
                    ->successNotificationTitle('Gitea user created'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
