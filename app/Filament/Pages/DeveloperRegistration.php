<?php

namespace App\Filament\Pages;

use App\Enums\DeveloperStatus;
use App\Enums\WorldGovernorate;
use App\Enums\SalaryCurrency;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\Skill;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Actions\Action;
use Filament\Support\RawJs;

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

    public function getMaxWidth(): Width | string | null
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
                            ->options(JobTitle::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        TextInput::make('years_of_experience')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(50)
                            ->suffix('years')
                            ->required(),

                        TextInput::make('expected_salary_from')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->label('Expected Salary From')
                            ->lt('expected_salary_to')
                            ->regex('/^\d+$/')
                            ->minValue(0),

                        TextInput::make('expected_salary_to')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->label('Expected Salary To')
                            ->gt('expected_salary_from')
                            ->regex('/^\d+$/')
                            ->minValue(0),

                        Select::make('salary_currency')
                            ->label('Salary Currency')
                            ->options(SalaryCurrency::class)
                            ->searchable()
                            ->default(SalaryCurrency::IQD),

                        Textarea::make('bio')
                            ->label('Bio / About You')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Tell us about your experience, skills, and what makes you unique...'),

                        Select::make('skills')
                            ->multiple()
                            ->options(Skill::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Links (Optional)')
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
                    ])
                    ->columns(3),

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

        // Extract skills before creating developer
        $skills = $data['skills'] ?? [];
        unset($data['skills']);

        // Create developer with pending status
        $data['status'] = DeveloperStatus::PENDING;
        $developer = Developer::create($data);

        // Attach skills
        if (!empty($skills)) {
            $developer->skills()->attach($skills);
        }

        // Show success notification
        Notification::make()
            ->title('Registration Submitted Successfully!')
            ->body('Your registration has been submitted and is pending approval. We\'ll review your profile soon!')
            ->success()
            ->send();

        // Reset form
        $this->form->fill();

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
