<?php

namespace App\Filament\Pages;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\DeveloperStatus;
use App\Enums\WorldGovernorate;
use App\Filament\Customs\ExpectedSalaryFromField;
use App\Filament\Customs\ExpectedSalaryToField;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\Skill;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;

class DeveloperRegistration extends SimplePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.developer-registration';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function hasTopbar(): bool
    {
        return false;
    }

    public function getMaxWidth(): Width|string|null
    {
        return Width::SevenExtraLarge;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->afterStateUpdatedJs(<<<'JS'
                            $set('slug', ($state).replaceAll(' ', '-').toLowerCase());
                            JS)
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique('developers', 'email')
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
                            ->label('Job Title')
                            ->options(fn () => JobTitle::active()->limit(50)->pluck('name', 'id'))
                            ->getSearchResultsUsing(fn (string $query) => JobTitle::active()->where('name', 'like', '%'.$query.'%')->limit(50)->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

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

                        Select::make('availability_type')
                            ->label('Availability Type')
                            ->options(AvailabilityType::class)
                            ->multiple()
                            ->searchable()
                            ->nullable()
                            ->helperText('Select your preferred work arrangement(s)'),

                        Textarea::make('bio')
                            ->label('Bio / About You')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Tell us about your experience, skills, and what makes you unique...'),

                        Select::make('skills')
                            ->multiple()
                            ->options(fn () => Skill::active()->limit(50)->pluck('name', 'id'))
                            ->preload()
                            ->getSearchResultsUsing(fn (string $query) => Skill::active()->where('name', 'like', '%'.$query.'%')->limit(50)->pluck('name', 'id'))
                            ->searchable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Links (Optional)')
                    ->description('The links are important for verification process of getting your login credentials. you can leave them empty if you don\'t have them.')
                    ->schema([
                        TextInput::make('portfolio_url')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-globe-alt')
                            ->placeholder('https://...'),

                        TextInput::make('github_url')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-code-bracket')
                            ->placeholder('https://github.com/...'),

                        TextInput::make('linkedin_url')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user-circle')
                            ->placeholder('https://linkedin.com/in/...'),

                        TextInput::make('youtube_url')
                            ->label('YouTube URL')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-play-circle')
                            ->placeholder('https://youtube.com/...')
                            ->helperText('A YouTube URL that explains yourself in a creative way'),
                    ])
                    ->columns(2),

                Checkbox::make('is_available')
                    ->label('I am available for hire')
                    ->default(true),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $this->validate();

        $data = $this->form->getState();

        $data['slug'] = Str::slug($data['name']);

        if (Developer::withoutGlobalScopes()->where('slug', $data['slug'])->exists()) {
            Notification::make()
                ->title('The name you provided is already taken')
                ->body('Please add extra information to your name to make it unique.')
                ->danger()
                ->send();

            return;
        }

        // Extract skills before creating developer
        $skills = $data['skills'] ?? [];
        unset($data['skills']);

        // Create developer with pending status
        $data['status'] = DeveloperStatus::PENDING;
        $developer = Developer::create($data);

        // Attach skills
        if (! empty($skills)) {
            $developer->skills()->attach($skills);
        }

        session()->flash('home_notification', [
            'title' => 'Registration Submitted Successfully!',
            'body' => 'Your registration has been submitted and is pending approval. We\'ll review your profile soon!',
        ]);

        $this->redirect(route('home'));
    }

    public function getTitle(): string
    {
        return 'Register as a Developer';
    }

    public function getHeading(): string
    {
        return 'Register as a Developer';
    }

    public function getSubheading(): ?string
    {
        return 'Join our platform and get discovered by clients';
    }

    public function getSubmitAction(): Action
    {
        return Action::make('submit')
            ->label('Submit Registration')
            ->submit('submit')
            ->extraAttributes([
                'style' => 'width: 100%; margin-top: 1rem;',
            ])
            ->icon('heroicon-o-check-circle');
    }
}
