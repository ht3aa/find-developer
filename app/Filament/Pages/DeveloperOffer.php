<?php

namespace App\Filament\Pages;

use App\Enums\AvailabilityType;
use App\Enums\OfferStatus;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\DeveloperOffer as ModelsDeveloperOffer;
use Filament\Actions\Action;
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
use Illuminate\Support\Facades\Auth;

class DeveloperOffer extends SimplePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected string $view = 'filament.pages.developer-offer';

    /**
     * Prevent this page from appearing in Filament navigation.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    /**
     * Get the slug for this page.
     */
    public static function getSlug(): string
    {
        return 'developer-offer';
    }

    public ?array $data = [];

    public ?Developer $targetDeveloper = null;

    public function mount(?string $developerSlug = null): void
    {
        if (! Auth::check()) {
            redirect()->route('filament.admin.auth.login');

            return;
        }

        if (! Auth::user()->isHR() && ! Auth::user()->isSuperAdmin()) {
            session()->flash('home_notification', [
                'title' => 'Access Denied',
                'body' => 'Only HR users can send offers to developers.',
            ]);
            redirect()->route('home');

            return;
        }

        if ($developerSlug) {
            $this->targetDeveloper = Developer::where('slug', $developerSlug)->firstOrFail();
        } else {
            redirect()->route('home');

            return;
        }

        $this->form->fill([
            'contact_email' => Auth::user()->email,
        ]);
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
                Section::make('Offer Details')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter your company name'),

                        Select::make('job_title_id')
                            ->label('Position / Job Title')
                            ->options(JobTitle::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Select position'),

                        TextInput::make('salary_range')
                            ->label('Salary Range (Optional)')
                            ->maxLength(255)
                            ->placeholder('e.g. 1,000,000 - 5,000,000 IQD/month'),

                        Select::make('work_type')
                            ->label('Work Type (Optional)')
                            ->options(AvailabilityType::class)
                            ->placeholder('Select work type'),

                        TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('your@email.com'),

                        Textarea::make('message')
                            ->label('Offer Message')
                            ->required()
                            ->rows(6)
                            ->placeholder('Describe the opportunity, requirements, benefits, and any other relevant details...')
                            ->helperText('This message will be shared with the developer once the offer is approved.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->description('Fill in the details of your offer for this developer.'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $this->validate();

        $formData = $this->form->getState();

        ModelsDeveloperOffer::create([
            'developer_id' => $this->targetDeveloper->id,
            'user_id' => Auth::id(),
            'company_name' => $formData['company_name'],
            'job_title_id' => $formData['job_title_id'],
            'message' => $formData['message'],
            'salary_range' => $formData['salary_range'] ?? null,
            'work_type' => $formData['work_type'] ?? null,
            'contact_email' => $formData['contact_email'],
            'status' => OfferStatus::PENDING,
        ]);

        session()->flash('home_notification', [
            'title' => 'Offer Submitted!',
            'body' => 'Your offer has been submitted and is pending approval. The developer will be notified once it is approved.',
        ]);

        $this->redirect(route('home'));
    }

    public function getTitle(): string
    {
        return 'Send Offer';
    }

    public function getHeading(): string
    {
        return 'Send Offer to ' . ($this->targetDeveloper?->name ?? 'Developer');
    }

    public function getSubheading(): ?string
    {
        return $this->targetDeveloper
            ? "Send a job offer to {$this->targetDeveloper->name}"
            : null;
    }

    public function getSubmitAction(): Action
    {
        return Action::make('submit')
            ->label('Submit Offer')
            ->submit('submit')
            ->extraAttributes([
                'style' => 'width: 100%; margin-top: 1rem;',
            ])
            ->icon('heroicon-o-paper-airplane');
    }
}
