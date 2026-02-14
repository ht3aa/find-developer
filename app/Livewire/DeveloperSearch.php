<?php

namespace App\Livewire;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\RecommendationStatus;
use App\Enums\SubscriptionPlan;
use App\Enums\WorldGovernorate;
use App\Filament\Customs\ExpectedSalaryFromField;
use App\Filament\Customs\ExpectedSalaryToField;
use App\Models\Badge;
use App\Models\Developer;
use App\Models\DeveloperRecommendation;
use App\Models\JobTitle;
use App\Models\Scopes\DeveloperScope;
use App\Models\Skill;
use App\Services\AiPromptBuilder;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DeveloperSearch extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public array $jobTitles = [];

    #[Url]
    public array $skills = [];

    #[Url]
    public array $locations = [];

    #[Url]
    public int $minExperience = 0;

    #[Url]
    public int $maxExperience = 50;

    #[Url]
    public string $expected_salary_from = '0';

    #[Url]
    public string $expected_salary_to = '0';

    #[Url]
    public ?Currency $salary_currency = null;

    #[Url]
    public array $availability_type = [];

    #[Url]
    public array $has_urls = [];

    #[Url]
    public array $badges = [];

    #[Url]
    public ?int $availableOnly = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filters')
                    ->collapsible()
                    ->description('Filter developers by various criteria')
                    ->schema([
                        TextInput::make('search')
                            ->label('Search')
                            ->placeholder('Name, email, location, skills...')
                            ->columnSpanFull()
                            ->live(debounce: 300)
                            ->hidden()
                            ->afterStateUpdated(fn () => $this->resetPage()),

                        Grid::make(2)
                            ->schema([
                                Select::make('jobTitles')
                                    ->label('Job Titles')
                                    ->multiple()
                                    ->searchable()
                                    ->options(JobTitle::active()->limit(50)->pluck('name', 'name'))
                                    ->preload()
                                    ->getSearchResultsUsing(fn (string $query) => JobTitle::active()->where('name', 'like', '%'.$query.'%')->limit(50)->pluck('name', 'name'))
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                Select::make('skills')
                                    ->label('Skills')
                                    ->multiple()
                                    ->searchable()
                                    ->options(Skill::active()->limit(50)->pluck('name', 'name'))
                                    ->preload()
                                    ->getSearchResultsUsing(fn (string $query) => Skill::active()->where('name', 'like', '%'.$query.'%')->limit(50)->pluck('name', 'name'))
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                Select::make('locations')
                                    ->label('Locations')
                                    ->multiple()
                                    ->searchable()
                                    ->options(
                                        collect(WorldGovernorate::cases())->mapWithKeys(
                                            fn ($case) => [$case->value => $case->getLabel()]
                                        )
                                    )
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                TextInput::make('minExperience')
                                    ->label('Min Experience (years)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('0')
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                TextInput::make('maxExperience')
                                    ->label('Max Experience (years)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('50')
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                ExpectedSalaryFromField::make()
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                ExpectedSalaryToField::make()
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                Select::make('salary_currency')
                                    ->label('Salary Currency')
                                    ->options(Currency::class)
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                Select::make('availability_type')
                                    ->label('Availability Type')
                                    ->options(AvailabilityType::class)
                                    ->multiple()
                                    ->searchable()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                Select::make('has_urls')
                                    ->label('Has URLs')
                                    ->options([
                                        'linkedin_url' => 'Has LinkedIn URL',
                                        'github_url' => 'Has GitHub URL',
                                        'portfolio_url' => 'Has Portfolio URL',
                                    ])
                                    ->multiple()
                                    ->searchable()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetPage()),

                                Select::make('badges')
                                    ->label('Badges')
                                    ->multiple()
                                    ->searchable()
                                    ->options(Badge::where('is_active', true)->orderBy('name')->limit(50)->pluck('name', 'id'))
                                    ->getSearchResultsUsing(fn (string $query) => Badge::where('is_active', true)->where('name', 'like', '%'.$query.'%')->orderBy('name')->limit(50)->pluck('name', 'id'))
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn () => $this->resetPage()),
                            ]),

                        Select::make('availableOnly')
                            ->label('Available only')
                            ->options([
                                1 => 'Available only',
                                0 => 'Unavailable',
                            ])
                            ->live()
                            ->placeholder('All')
                            ->afterStateUpdated(fn () => $this->resetPage()),
                    ]),
            ]);
    }

    public function clearFilters(): void
    {
        $this->reset();
    }

    public function getActiveFiltersCount(): int
    {
        $count = 0;

        // Count search filter
        if (! empty($this->search)) {
            $count++;
        }

        // Count array filters
        if (! empty($this->jobTitles)) {
            $count++;
        }
        if (! empty($this->skills)) {
            $count++;
        }
        if (! empty($this->locations)) {
            $count++;
        }
        if (! empty($this->availability_type)) {
            $count++;
        }
        if (! empty($this->has_urls)) {
            $count++;
        }
        if (! empty($this->badges)) {
            $count++;
        }

        // Count experience filters
        if ($this->minExperience > 0) {
            $count++;
        }
        if ($this->maxExperience < 50) {
            $count++;
        }

        // Count salary filters
        if (! empty($this->expected_salary_from) && $this->expected_salary_from !== '0') {
            $count++;
        }
        if (! empty($this->expected_salary_to) && $this->expected_salary_to !== '0') {
            $count++;
        }
        if ($this->salary_currency !== null) {
            $count++;
        }

        // Count availability filter
        if ($this->availableOnly !== null) {
            $count++;
        }

        return $count;
    }

    /**
     * Query array in the shape expected by AiPromptBuilder (for live-updating AI prompt in filter panel).
     *
     * @return array<string, mixed>
     */
    public function getQueryArrayForAiPrompt(): array
    {
        return [
            'search' => $this->search,
            'jobTitles' => $this->jobTitles,
            'skills' => $this->skills,
            'locations' => $this->locations,
            'minExperience' => $this->minExperience,
            'maxExperience' => $this->maxExperience,
            'expected_salary_from' => $this->expected_salary_from,
            'expected_salary_to' => $this->expected_salary_to,
            'salary_currency' => $this->salary_currency?->value,
            'availability_type' => $this->availability_type,
            'has_urls' => $this->has_urls,
            'badges' => $this->badges,
            'availableOnly' => $this->availableOnly,
        ];
    }

    /**
     * @return array{promptText: string, searchUrl: string, hasFilters: bool}
     */
    public function getAiPromptData(): array
    {
        return AiPromptBuilder::build($this->getQueryArrayForAiPrompt());
    }

    protected function buildBaseQuery(array $filters)
    {
        return Developer::with(['jobTitle', 'skills', 'badges'])
            ->with(['projects' => function ($query) {
                $query->withoutGlobalScopes([DeveloperScope::class])
                    ->where('show_project', true)
                    ->limit(6)
                    ->orderBy('created_at', 'desc');
            }])
            ->withCount(['projects' => function ($query) {
                $query->withoutGlobalScopes([DeveloperScope::class])
                    ->where('show_project', true);
            }])
            ->withCount(['recommendationsReceived' => function ($query) {
                $query->where('status', RecommendationStatus::APPROVED);
            }])
            ->withCount([
                'badges',
                'badges as has_experience_assessment_badge_count' => fn ($query) => $query->where('slug', config('badge.experience-assessment-badge')),
            ])
            ->when(! empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('email', 'like', '%'.$filters['search'].'%')
                        ->orWhere('location', 'like', '%'.$filters['search'].'%')
                        ->orWhereHas('skills', function ($skillQuery) use ($filters) {
                            $skillQuery->where('name', 'like', '%'.$filters['search'].'%');
                        });
                });
            })
            ->when(! empty($filters['jobTitles']), function ($query) use ($filters) {
                $query->whereHas('jobTitle', function ($jobTitleQuery) use ($filters) {
                    $jobTitleQuery->whereIn('name', $filters['jobTitles']);
                });
            })
            ->when(! empty($filters['skills']), function ($query) use ($filters) {
                $query->whereHas('skills', function ($skillQuery) use ($filters) {
                    $skillQuery->whereIn('name', $filters['skills']);
                });
            })
            ->when(! empty($filters['locations']), function ($query) use ($filters) {
                $query->whereIn('location', $filters['locations']);
            })
            ->when(! empty($filters['minExperience']), function ($query) use ($filters) {
                $query->where('years_of_experience', '>=', $filters['minExperience']);
            })
            ->when(! empty($filters['maxExperience']), function ($query) use ($filters) {
                $query->where('years_of_experience', '<=', $filters['maxExperience']);
            })
            ->when(! empty($filters['expected_salary_from']) && $filters['expected_salary_to'] === null, function ($query) use ($filters) {
                $query->where('expected_salary_from', '>=', Str::of($filters['expected_salary_from'])->replace(',', '')->toInteger());
            })
            ->when(! empty($filters['expected_salary_to']) && $filters['expected_salary_from'] === null, function ($query) use ($filters) {
                $query->where('expected_salary_to', '<=', Str::of($filters['expected_salary_to'])->replace(',', '')->toInteger());
            })
            ->when(! empty($filters['expected_salary_from']) && ! empty($filters['expected_salary_to']), function ($query) use ($filters) {
                $query->whereBetween('expected_salary_from', [Str::of($filters['expected_salary_from'])->replace(',', '')->toInteger(), Str::of($filters['expected_salary_to'])->replace(',', '')->toInteger()]);
            })
            ->when(! empty($filters['salary_currency']), function ($query) use ($filters) {
                $query->where('salary_currency', $filters['salary_currency']);
            })
            ->when(! empty($filters['availability_type']), function ($query) use ($filters) {
                $types = is_array($filters['availability_type'])
                    ? $filters['availability_type']
                    : [$filters['availability_type']];

                $query->where(function ($q) use ($types) {
                    foreach ($types as $type) {
                        $q->orWhereJsonContains('availability_type', $type instanceof AvailabilityType ? $type->value : $type);
                    }
                });
            })
            ->when(! empty($filters['has_urls']), function ($query) use ($filters) {
                $urls = is_array($filters['has_urls'])
                    ? $filters['has_urls']
                    : [$filters['has_urls']];

                $query->where(function ($q) use ($urls) {
                    foreach ($urls as $urlField) {
                        $q->orWhere(function ($subQ) use ($urlField) {
                            $subQ->whereNotNull($urlField)
                                ->where($urlField, '!=', '');
                        });
                    }
                });
            })
            ->when(! empty($filters['badges']), function ($query) use ($filters) {
                $badgeIds = is_array($filters['badges']) ? $filters['badges'] : [$filters['badges']];
                $query->whereHas('badges', fn ($q) => $q->whereIn('badges.id', $badgeIds));
            })
            ->when(! is_null($filters['availableOnly']), function ($query) use ($filters) {
                $query->where('is_available', $filters['availableOnly']);
            });
    }

    protected function getViewData(array $filters): array
    {
        $baseQuery = $this->buildBaseQuery($filters);

        // Get developers by subscription plan; order by validated-data badge first, then badge count, then recommendations
        $badgeOrder = fn ($query) => $query
            ->orderBy('has_experience_assessment_badge_count', 'desc')
            ->orderBy('badges_count', 'desc')
            ->orderBy('recommendations_received_count', 'desc')
            ->orderBy('id', 'asc');

        $premiumDevelopers = (clone $baseQuery)
            ->where('subscription_plan', SubscriptionPlan::PREMIUM)
            ->tap($badgeOrder)
            ->get();

        $proDevelopers = (clone $baseQuery)
            ->where('subscription_plan', SubscriptionPlan::PRO)
            ->tap($badgeOrder)
            ->get();

        $freeDevelopers = (clone $baseQuery)
            ->where('subscription_plan', SubscriptionPlan::FREE)
            ->tap($badgeOrder)
            ->paginate(15);

        $totalCount = $premiumDevelopers->count() + $proDevelopers->count() + $freeDevelopers->total();

        Auth::user()?->loadMissing('developer');
        $currentUserDeveloper = Auth::check() ? Auth::user()->developer : null;
        $recommendedDeveloperIds = $currentUserDeveloper
            ? DeveloperRecommendation::where('recommender_id', $currentUserDeveloper->id)
                ->whereIn('status', [RecommendationStatus::PENDING, RecommendationStatus::APPROVED])
                ->pluck('recommended_id')
                ->all()
            : [];

        return [
            'premiumDevelopers' => $premiumDevelopers,
            'proDevelopers' => $proDevelopers,
            'freeDevelopers' => $freeDevelopers,
            'totalCount' => $totalCount,
            'activeFiltersCount' => $this->getActiveFiltersCount(),
            'currentUserDeveloper' => $currentUserDeveloper,
            'recommendedDeveloperIds' => $recommendedDeveloperIds,
            'aiPromptData' => $this->getAiPromptData(),
        ];
    }

    public function render()
    {
        $filters = $this->all();

        return view('livewire.developer-search', $this->getViewData($filters));
    }
}
