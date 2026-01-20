<?php

namespace App\Filament\Resources\Developers\Tables;

use App\Enums\DeveloperStatus;
use App\Enums\AvailabilityType;
use App\Enums\UserType;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;
use Spatie\Permission\Models\Role;

class DevelopersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jobTitle.name')
                    ->label('Job Title')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('years_of_experience')
                    ->label('Experience')
                    ->sortable()
                    ->suffix(' years'),

                TextColumn::make('expected_salary_from')
                    ->label('Salary From')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        return number_format($state) . ' ' . $record->currency;
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('expected_salary_to')
                    ->label('Salary To')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';
                        return number_format($state) . ' ' . $record->currency;
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('subscription_plan')
                    ->label('Plan')
                    ->badge()
                    ->sortable(),

                ToggleColumn::make('is_available')
                    ->label('Available')
                    ->sortable(),

                TextColumn::make('availability_type')
                    ->label('Availability Type')
                    ->formatStateUsing(function ($state) {
                        if (empty($state) || !is_array($state)) {
                            return null;
                        }
                        return collect($state)->map(fn($type) => $type->getLabel())->toArray();
                    })
                    ->badge()
                    ->separator(',')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('badges.name')
                    ->label('Badges')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->toggleable(),

                ToggleColumn::make('recommended_by_us')
                    ->label('Recommended By Us')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(DeveloperStatus::class)
                    ->label('Status'),

                SelectFilter::make('job_title_id')
                    ->relationship('jobTitle', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Job Title'),

                TernaryFilter::make('is_available')
                    ->label('Availability')
                    ->boolean()
                    ->trueLabel('Available only')
                    ->falseLabel('Unavailable only')
                    ->native(false),

                SelectFilter::make('availability_type')
                    ->label('Availability Type')
                    ->options(AvailabilityType::class)
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            $value = $data['value'];
                            // Convert enum to string value if needed
                            if ($value instanceof AvailabilityType) {
                                $value = $value->value;
                            }
                            $query->whereJsonContains('availability_type', $value);
                        }
                    }),

                TernaryFilter::make('recommended_by_us')
                    ->label('Recommended By Us')
                    ->boolean()
                    ->trueLabel('Recommended only')
                    ->falseLabel('Not recommended only')
                    ->native(false),

                Filter::make('years_of_experience')
                    ->form([
                        TextInput::make('min_experience')
                            ->numeric()
                            ->label('Minimum Years'),
                        TextInput::make('max_experience')
                            ->numeric()
                            ->label('Maximum Years'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min_experience'], fn($query, $value) => $query->where('years_of_experience', '>=', $value))
                            ->when($data['max_experience'], fn($query, $value) => $query->where('years_of_experience', '<=', $value));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('create_user')
                        ->label('Create User')
                        ->icon('heroicon-o-user-plus')
                        ->color('primary')
                        ->visible(fn($record) => !$record->user_id)
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->default(fn($record) => $record->name),

                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->rules([
                                    new Unique(User::class, 'email')
                                ])
                                ->maxLength(255)
                                ->default(fn($record) => $record->email),

                            TextInput::make('linkedin_url')
                                ->label('LinkedIn URL')
                                ->url()
                                ->nullable()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-o-link')
                                ->helperText('Enter the full LinkedIn profile URL (e.g., https://linkedin.com/in/username)')
                                ->default(fn($record) => $record->linkedin_url),

                            Select::make('user_type')
                                ->label('User Type')
                                ->options(UserType::class)
                                ->default(UserType::DEVELOPER)
                                ->required()
                                ->searchable(),

                            TextInput::make('password')
                                ->password()
                                ->rules([Password::default()])
                                ->required()
                                ->dehydrateStateUsing(fn($state) => bcrypt($state)),

                            Toggle::make('can_access_admin_panel')
                                ->label('Can Access Admin Panel')
                                ->default(false)
                                ->required(),

                            Select::make('role')
                                ->label('Role')
                                ->options(fn() => Role::all()->pluck('name', 'name'))
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $user = User::create([
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'password' => $data['password'],
                                'linkedin_url' => $data['linkedin_url'] ?? null,
                                'user_type' => $data['user_type'],
                                'can_access_admin_panel' => $data['can_access_admin_panel'],
                            ]);

                            // Assign role
                            if (!empty($data['role'])) {
                                $user->assignRole($data['role']);
                            }

                            // Link user to developer
                            $record->update(['user_id' => $user->id]);

                            Notification::make()
                                ->title('User Created')
                                ->body("User account has been created for {$record->name}.")
                                ->success()
                                ->send();
                        }),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status !== DeveloperStatus::APPROVED)
                        ->action(function ($record) {
                            $record->update(['status' => DeveloperStatus::APPROVED]);

                            Notification::make()
                                ->title('Developer Approved')
                                ->body("Developer {$record->name} has been approved.")
                                ->success()
                                ->send();
                        }),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status !== DeveloperStatus::REJECTED)
                        ->action(function ($record) {
                            $record->update(['status' => DeveloperStatus::REJECTED]);

                            Notification::make()
                                ->title('Developer Rejected')
                                ->body("Developer {$record->name} has been rejected.")
                                ->warning()
                                ->send();
                        }),

                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $count = $records->count();

                            $records->each(function ($record) {
                                $record->update(['status' => DeveloperStatus::APPROVED]);
                            });

                            Notification::make()
                                ->title('Developers Approved')
                                ->body("{$count} developer(s) have been approved.")
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $count = $records->count();

                            $records->each(function ($record) {
                                $record->update(['status' => DeveloperStatus::REJECTED]);
                            });

                            Notification::make()
                                ->title('Developers Rejected')
                                ->body("{$count} developer(s) have been rejected.")
                                ->warning()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
