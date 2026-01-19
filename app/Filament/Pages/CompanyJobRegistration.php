<?php

namespace App\Filament\Pages;

use App\Enums\JobStatus;
use App\Enums\WorldGovernorate;
use App\Enums\Currency;
use App\Filament\Customs\ExpectedSalaryFromField;
use App\Filament\Customs\ExpectedSalaryToField;
use App\Models\CompanyJob;
use App\Models\JobTitle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Actions\Action;

class CompanyJobRegistration extends SimplePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected string $view = 'filament.pages.company-job-registration';

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
                Section::make('Job Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Job Title')
                            ->placeholder('e.g., Senior PHP Developer'),

                        Select::make('job_title_id')
                            ->label('Job Category')
                            ->options(JobTitle::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Textarea::make('description')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->placeholder('Describe the job position, responsibilities, and what you\'re looking for...'),

                        Textarea::make('requirements')
                            ->label('Requirements')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('List any specific requirements or qualifications needed...'),
                    ])
                    ->columns(2),

                Section::make('Company Information')
                    ->schema([
                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Company Name'),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label('Contact Email'),

                        TextInput::make('contact_link')
                            ->url()
                            ->maxLength(255)
                            ->label('Contact Link')
                            ->placeholder('https://...'),

                        Select::make('location')
                            ->label('Location')
                            ->options(WorldGovernorate::class)
                            ->searchable(),
                    ])
                    ->columns(2),

                Section::make('Salary & Compensation')
                    ->schema([
                        ExpectedSalaryFromField::make('salary_from'),
                        ExpectedSalaryToField::make('salary_to'),

                        Select::make('salary_currency')
                            ->label('Salary Currency')
                            ->options(Currency::class)
                            ->searchable()
                            ->default(Currency::IQD),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $this->validate();

        $data = $this->form->getState();

        // Create job with pending status
        $data['status'] = JobStatus::PENDING;
        $job = CompanyJob::create($data);

        // Show success notification
        Notification::make()
            ->title('Job Posted Successfully!')
            ->body('Your job offer has been submitted and is pending approval. We\'ll review it soon!')
            ->success()
            ->send();

        // Reset form
        $this->form->fill();

        $this->redirect(route('home'));
    }

    public function getTitle(): string
    {
        return 'Post a Job Offer';
    }

    public function getHeading(): string
    {
        return 'Post a Job Offer';
    }

    public function getSubheading(): ?string
    {
        return 'Find talented developers for your team';
    }

    public function getSubmitAction(): Action
    {
        return Action::make('submit')
            ->label('Submit Job Offer')
            ->submit('submit')
            ->extraAttributes([
                'style' => 'width: 100%; margin-top: 1rem;',
            ])
            ->icon('heroicon-o-check-circle');
    }
}
