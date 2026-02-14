<?php

namespace App\Filament\Pages;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\DeveloperStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\WorldGovernorate;
use App\Filament\Customs\ExpectedSalaryFromField;
use App\Filament\Customs\ExpectedSalaryToField;
use App\Models\Badge;
use App\Models\Developer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class DeveloperProfile extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected string $view = 'filament.pages.developer-profile';

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Developer Profile';

    public ?Developer $record = null;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->isDeveloper();
    }

    public function mount(): void
    {
        $this->record = auth()->user()->developer;

        $data = $this->record->toArray();

        $data['skills'] = $this->record->skills->pluck('id')->toArray();

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->record)
            ->statePath('data')
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),

                        Select::make('location')
                            ->options(WorldGovernorate::class)
                            ->searchable(),
                    ])
                    ->columns(2),

                Section::make('Professional Information')
                    ->schema([
                        Select::make('job_title_id')
                            ->relationship('jobTitle', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->rows(3),
                            ]),

                        TextInput::make('years_of_experience')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(50)
                            ->suffix('years')
                            ->required(),

                        ExpectedSalaryFromField::make(),

                        ExpectedSalaryToField::make(),

                        Select::make('salary_currency')
                            ->label('Salary Currency')
                            ->options(Currency::class)
                            ->searchable()
                            ->default(Currency::IQD),

                        Toggle::make('is_available')
                            ->label('Available for hire')
                            ->default(true)
                            ->required(),

                        Select::make('availability_type')
                            ->label('Availability Type')
                            ->options(AvailabilityType::class)
                            ->multiple()
                            ->searchable()
                            ->nullable()
                            ->helperText('Select your preferred work arrangement(s)'),

                        Textarea::make('bio')
                            ->rows(4)
                            ->columnSpanFull(),

                        Select::make('skills')
                            ->relationship('skills', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Links')
                    ->schema([
                        TextInput::make('portfolio_url')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-globe-alt'),

                        TextInput::make('github_url')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-code-bracket'),

                        TextInput::make('linkedin_url')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user-circle'),
                    ])
                    ->columns(3),

                Section::make('CV / Resume')
                    ->schema([
                        FileUpload::make('cv_path')
                            ->label('CV / Resume (PDF only)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('developer-cvs')
                            ->maxSize(1024)
                            ->helperText('Upload a PDF version of the developer\'s CV. Max 1MP.'),
                    ]),
            ]);
    }

    public function getSubscriptionPlan(): SubscriptionPlan
    {
        return $this->record->subscription_plan;
    }

    public function getSubscriptionPlanConfig(): array
    {
        $subscriptionPlan = $this->getSubscriptionPlan();

        $planConfig = [
            'free' => [
                'bg' => 'bg-gray-50',
                'text' => 'text-gray-700',
                'border' => 'border-gray-200',
                'badge' => 'bg-gray-100 text-gray-800',
            ],
            'pro' => [
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
                'border' => 'border-blue-200',
                'badge' => 'bg-blue-100 text-blue-800',
            ],
            'premium' => [
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-700',
                'border' => 'border-amber-200',
                'badge' => 'bg-amber-100 text-amber-800',
            ],
        ];

        return $planConfig[$subscriptionPlan->value] ?? $planConfig['free'];
    }

    public function save(): void
    {
        $this->validate();

        $data = $this->form->getState();

        $data['expected_salary_from'] = Str::of($data['expected_salary_from'])->remove(',')->toInteger();
        $data['expected_salary_to'] = Str::of($data['expected_salary_to'])->remove(',')->toInteger();

        if ((int) $data['years_of_experience'] !== $this->record->years_of_experience) {
            $data['status'] = DeveloperStatus::EXPERIENCE_CHANGED;
            $this->record->badges()->detach(Badge::where('slug', config('badge.experience-assessment-badge'))->first()->id);
        }

        $this->record->update($data);

        Notification::make()
            ->title('Profile Updated')
            ->body('Your developer profile has been updated successfully.')
            ->success()
            ->send();
    }

    public static function getSaveAction(): Action
    {
        return Action::make('save')
            ->label('Save Changes')
            ->action(fn () => $this->save())
            ->submit('save')
            ->extraAttributes([
                'style' => 'width: 100%; margin-top: 1rem;',
            ])
            ->keyBindings(['mod+s'])
            ->icon('heroicon-o-check-circle');
    }
}
