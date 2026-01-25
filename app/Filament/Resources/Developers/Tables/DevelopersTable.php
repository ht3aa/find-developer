<?php

namespace App\Filament\Resources\Developers\Tables;

use App\Enums\DeveloperStatus;
use App\Enums\AvailabilityType;
use App\Enums\UserType;
use App\Models\User;
use App\Notifications\MailtrapNotification;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

                    Action::make('send_email')
                        ->label('Send Email')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->visible(fn($record) => !empty($record->email))
                        ->schema([
                            TextInput::make('subject')
                                ->label('Subject')
                                ->required()
                                ->maxLength(255)
                                ->default('Message from Find Developer'),

                            Textarea::make('message')
                                ->label('Message')
                                ->required()
                                ->rows(5)
                                ->columnSpanFull(),

                            Toggle::make('add_default_header')
                                ->label('Add default header to the mail')
                                ->default(true)
                                ->helperText('Adds "Hello {name}" at the beginning of the message'),

                            Toggle::make('add_default_footer')
                                ->label('Add default footer')
                                ->default(true)
                                ->helperText('Adds "Best Regards\nHasan Tahseen an admin in www.find-developer.com" at the end of the message'),

                            TextInput::make('category')
                                ->label('Category')
                                ->maxLength(255)
                                ->placeholder('Optional category for tracking')
                                ->helperText('Optional: Add a category to track this email type'),
                        ])
                        ->action(function ($record, array $data) {
                            try {
                                $message = $data['message'];

                                // Add header if enabled
                                if ($data['add_default_header'] ?? true) {
                                    $message = "Hello {$record->name}\n\n" . $message;
                                }

                                // Add footer if enabled
                                if ($data['add_default_footer'] ?? true) {
                                    $message .= "\n\nBest Regards\nHasan Tahseen an admin in www.find-developer.com";
                                }

                                $record->notify(new MailtrapNotification(
                                    subject: $data['subject'],
                                    message: $message,
                                    category: $data['category'] ?? 'Admin Message'
                                ));

                                Notification::make()
                                    ->title('Email Sent')
                                    ->body("Email has been sent to {$record->email}.")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Email Failed')
                                    ->body("Failed to send email: {$e->getMessage()}")
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Action::make('send_credentials_email')
                        ->label('Send User Credentials')
                        ->icon('heroicon-o-key')
                        ->color('success')
                        ->visible(fn($record) => !empty($record->email))
                        ->schema([
                            TextInput::make('secret_url')
                                ->label('Secret URL')
                                ->required()
                                ->url()
                                ->maxLength(500)
                                ->placeholder('https://example.com/reset-password?token=...')
                                ->helperText('Enter the URL for user credentials (password reset or account activation link)'),
                        ])
                        ->action(function ($record, array $data) {
                            try {
                                $message = "Hello {$record->name}\n\n";
                                $message .= "Thank you for the information. You have been accepted and this is your user credentials\n";
                                $message .= $data['secret_url'] . "\n\n";
                                $message .= "You can edit your information via admin dashboard\n";
                                $message .= "www.find-developer.com/admin\n\n";
                                $message .= "You can now also recommend other developers. Please use the recommendation feature only on the developers you well known\n\n";
                                $message .= "Best Regards\n";
                                $message .= "Hasan Tahseen an Admin in find-developer.com platform";

                                $record->notify(new MailtrapNotification(
                                    subject: 'User Credentials Created',
                                    message: $message,
                                    category: 'User Credentials'
                                ));

                                Notification::make()
                                    ->title('Credentials Email Sent')
                                    ->body("User credentials email has been sent to {$record->email}.")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Email Failed')
                                    ->body("Failed to send credentials email: {$e->getMessage()}")
                                    ->danger()
                                    ->send();
                            }
                        }),

                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('copy_emails')
                        ->label('Copy Emails')
                        ->icon('heroicon-o-clipboard-document')
                        ->color('gray')
                        ->modalHeading('Copy Emails')
                        ->modalDescription('Click the copy button next to the field to copy all selected developers\' emails to your clipboard.')
                        ->modalSubmitAction(false)
                        ->schema([
                            TextInput::make('emails')
                                ->label('Developer emails')
                                ->readOnly()
                                ->copyable(copyMessage: 'Emails copied to clipboard')
                                ->columnSpanFull(),
                        ])
                        ->fillForm(fn(Collection $records): array => [
                            'emails' => $records->pluck('email')->filter()->implode(', '),
                        ])
                        ->action(fn() => null),

                    BulkAction::make('send_bulk_email')
                        ->label('Send Bulk Email')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Send Bulk Email')
                        ->modalDescription(fn(Collection $records) => "Send an email to {$records->filter(fn($record) => !empty($record->email))->count()} developer(s) with email addresses.")
                        ->schema([
                            TextInput::make('subject')
                                ->label('Subject')
                                ->required()
                                ->maxLength(255)
                                ->default('Message from Find Developer'),

                            Textarea::make('message')
                                ->label('Message')
                                ->required()
                                ->rows(5)
                                ->columnSpanFull(),

                            Toggle::make('add_default_header')
                                ->label('Add default header to the mail')
                                ->default(true)
                                ->helperText('Adds "Hello {name}" at the beginning of the message'),

                            Toggle::make('add_default_footer')
                                ->label('Add default footer')
                                ->default(true)
                                ->helperText('Adds "Best Regards\nHasan Tahseen an admin in www.find-developer.com" at the end of the message'),

                            TextInput::make('category')
                                ->label('Category')
                                ->maxLength(255)
                                ->placeholder('Optional category for tracking')
                                ->helperText('Optional: Add a category to track this email type'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $developersWithEmail = $records->filter(fn($record) => !empty($record->email));

                            if ($developersWithEmail->isEmpty()) {
                                Notification::make()
                                    ->title('No Emails Found')
                                    ->body('None of the selected developers have email addresses.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $count = $developersWithEmail->count();
                            $category = $data['category'] ?? 'Bulk Email';
                            $addHeader = $data['add_default_header'] ?? true;
                            $addFooter = $data['add_default_footer'] ?? true;

                            try {
                                foreach ($developersWithEmail as $developer) {
                                    $message = $data['message'];

                                    // Add header if enabled
                                    if ($addHeader) {
                                        $message = "Hello {$developer->name}\n\n" . $message;
                                    }

                                    // Add footer if enabled
                                    if ($addFooter) {
                                        $message .= "\n\nBest Regards\nHasan Tahseen an admin in www.find-developer.com";
                                    }

                                    $developer->notify(new MailtrapNotification(
                                        subject: $data['subject'],
                                        message: $message,
                                        category: $category
                                    ));
                                }

                                Notification::make()
                                    ->title('Bulk Email Sent')
                                    ->body("Email has been sent to {$count} developer(s).")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Bulk Email Failed')
                                    ->body("Failed to send bulk email: {$e->getMessage()}")
                                    ->danger()
                                    ->send();
                            }
                        }),

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
