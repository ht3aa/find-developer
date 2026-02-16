<?php

namespace App\Filament\Pages;

use App\Enums\RecommendationStatus;
use App\Models\Developer;
use App\Models\DeveloperRecommendation as ModelsDeveloperRecommendation;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class DeveloperRecommendation extends SimplePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected string $view = 'filament.pages.developer-recommendation';

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
        return 'developer-recommendation';
    }

    public ?array $data = [];

    public ?Developer $recommendedDeveloper = null;

    public function mount(?int $developer = null): void
    {
        // Ensure user is authenticated and is a developer
        if (! Auth::check() || ! Auth::user()->isDeveloper()) {
            redirect()->route('filament.admin.auth.login');

            return;
        }

        // Get the developer to recommend
        if ($developer) {
            $this->recommendedDeveloper = Developer::findOrFail($developer);

            // Prevent self-recommendation
            $recommender = Auth::user()->developer;
            if ($recommender && $recommender->id === $this->recommendedDeveloper->id) {
                Notification::make()
                    ->title('Invalid Action')
                    ->body('You cannot recommend yourself.')
                    ->danger()
                    ->send();
                redirect()->route('home');

                return;
            }

            // Check if already recommended
            $existing = ModelsDeveloperRecommendation::where('recommender_id', $recommender->id)
                ->where('recommended_id', $this->recommendedDeveloper->id)
                ->first();

            if ($existing) {
                Notification::make()
                    ->title('Already Recommended')
                    ->body('You have already recommended this developer.')
                    ->warning()
                    ->send();
                redirect()->route('home');

                return;
            }
        } else {
            redirect()->route('home');

            return;
        }

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
                Section::make('Recommendation Details')
                    ->schema([
                        Textarea::make('recommendation_note')
                            ->label('Why do you recommend this developer?')
                            ->required()
                            ->rows(6)
                            ->placeholder('Please explain why you recommend this developer. Include details about their skills, work ethic, experience, or any other relevant information...')
                            ->helperText('This information will help others understand why this developer is recommended.')
                            ->columnSpanFull(),
                    ])
                    ->description('Share your thoughts on why this developer is worth recommending.'),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $this->validate();

        $recommender = Auth::user()->developer;

        // Create recommendation with pending status
        ModelsDeveloperRecommendation::create([
            'recommender_id' => $recommender->id,
            'recommended_id' => $this->recommendedDeveloper->id,
            'recommendation_note' => $this->form->getState()['recommendation_note'],
            'status' => RecommendationStatus::PENDING,
        ]);

        session()->flash('home_notification', [
            'title' => 'Recommendation Submitted!',
            'body' => 'Your recommendation has been submitted and is pending approval. Thank you for helping the community!',
        ]);

        $this->redirect(route('home'));
    }

    public function getTitle(): string
    {
        return 'Recommend Developer';
    }

    public function getHeading(): string
    {
        return 'Recommend '.($this->recommendedDeveloper?->name ?? 'Developer');
    }

    public function getSubheading(): ?string
    {
        return $this->recommendedDeveloper
            ? "Share why you recommend {$this->recommendedDeveloper->name}"
            : null;
    }

    public function getSubmitAction(): Action
    {
        return Action::make('submit')
            ->label('Submit Recommendation')
            ->submit('submit')
            ->extraAttributes([
                'style' => 'width: 100%; margin-top: 1rem;',
            ])
            ->icon('heroicon-o-check-circle');
    }
}
