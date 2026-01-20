<?php

namespace App\Livewire;

use App\Enums\DeveloperStatus;
use App\Enums\Currency;
use App\Enums\WorldGovernorate;
use App\Enums\SubscriptionPlan;
use App\Enums\AvailabilityType;
use App\Filament\Customs\ExpectedSalaryFromField;
use App\Filament\Customs\ExpectedSalaryToField;
use App\Models\Developer;
use App\Models\JobTitle;
use App\Models\Scopes\DeveloperScope;
use App\Models\Skill;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

class DeveloperSearch extends Component implements HasSchemas, HasActions
{
    use WithPagination;
    use InteractsWithSchemas;
    use InteractsWithActions;


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
    public int $expected_salary_from = 0;
    #[Url]
    public int $expected_salary_to = 0;
    #[Url]
    public ?Currency $salary_currency = null;

    #[Url]
    public array $availability_type = [];

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
                            ->afterStateUpdated(fn() => $this->resetPage()),

                        Grid::make(2)
                            ->schema([
                                Select::make('jobTitles')
                                    ->label('Job Titles')
                                    ->multiple()
                                    ->searchable()
                                    ->options(JobTitle::active()->limit(50)->pluck('name', 'name'))
                                    ->preload()
                                    ->getSearchResultsUsing(fn(string $query) => JobTitle::active()->where('name', 'like', '%' . $query . '%')->limit(50)->pluck('name', 'name'))
                                    ->live()
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                Select::make('skills')
                                    ->label('Skills')
                                    ->multiple()
                                    ->searchable()
                                    ->options(Skill::active()->limit(50)->pluck('name', 'name'))
                                    ->preload()
                                    ->getSearchResultsUsing(fn(string $query) => Skill::active()->where('name', 'like', '%' . $query . '%')->limit(50)->pluck('name', 'name'))
                                    ->live()
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                Select::make('locations')
                                    ->label('Locations')
                                    ->multiple()
                                    ->searchable()
                                    ->options(
                                        collect(WorldGovernorate::cases())->mapWithKeys(
                                            fn($case) => [$case->value => $case->getLabel()]
                                        )
                                    )
                                    ->live()
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                TextInput::make('minExperience')
                                    ->label('Min Experience (years)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('0')
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                TextInput::make('maxExperience')
                                    ->label('Max Experience (years)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('50')
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                ExpectedSalaryFromField::make()
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                ExpectedSalaryToField::make()
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                Select::make('salary_currency')
                                    ->label('Salary Currency')
                                    ->options(Currency::class)
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                Select::make('availability_type')
                                    ->label('Availability Type')
                                    ->options(AvailabilityType::class)
                                    ->multiple()
                                    ->searchable()
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(fn() => $this->resetPage()),
                            ]),

                        Select::make('availableOnly')
                            ->label('Available only')
                            ->options([
                                1 => 'Available only',
                                0 => 'Unavailable',
                            ])
                            ->live()
                            ->placeholder('All')
                            ->afterStateUpdated(fn() => $this->resetPage()),
                    ])
            ]);
    }

    public function clearFilters(): void
    {
        $this->reset();
    }

    public function render()
    {
        $filters = $this->all();

        $baseQuery = Developer::with(['jobTitle', 'skills'])
            ->with(['projects' => function ($query) {
                $query->withoutGlobalScopes([DeveloperScope::class])->limit(6)->orderBy('created_at', 'desc');
            }])
            ->withCount(['projects' => function ($query) {
                $query->withoutGlobalScopes([DeveloperScope::class]);
            }])
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('location', 'like', '%' . $filters['search'] . '%')
                        ->orWhereHas('skills', function ($skillQuery) use ($filters) {
                            $skillQuery->where('name', 'like', '%' . $filters['search'] . '%');
                        });
                });
            })
            ->when(!empty($filters['jobTitles']), function ($query) use ($filters) {
                $query->whereHas('jobTitle', function ($jobTitleQuery) use ($filters) {
                    $jobTitleQuery->whereIn('name', $filters['jobTitles']);
                });
            })
            ->when(!empty($filters['skills']), function ($query) use ($filters) {
                $query->whereHas('skills', function ($skillQuery) use ($filters) {
                    $skillQuery->whereIn('name', $filters['skills']);
                });
            })
            ->when(!empty($filters['locations']), function ($query) use ($filters) {
                $query->whereIn('location', $filters['locations']);
            })
            ->when(!empty($filters['minExperience']), function ($query) use ($filters) {
                $query->where('years_of_experience', '>=', $filters['minExperience']);
            })
            ->when(!empty($filters['maxExperience']), function ($query) use ($filters) {
                $query->where('years_of_experience', '<=', $filters['maxExperience']);
            })
            ->when(!empty($filters['expected_salary_from']) && $filters['expected_salary_to'] === null, function ($query) use ($filters) {
                $query->where('expected_salary_from', '>=',  Str::of($filters['expected_salary_from'])->replace(',', '')->toInteger());
            })
            ->when(!empty($filters['expected_salary_to']) && $filters['expected_salary_from'] === null, function ($query) use ($filters) {
                $query->where('expected_salary_to', '<=', Str::of($filters['expected_salary_to'])->replace(',', '')->toInteger());
            })
            ->when(!empty($filters['expected_salary_from']) && !empty($filters['expected_salary_to']), function ($query) use ($filters) {
                $query->whereBetween('expected_salary_from', [Str::of($filters['expected_salary_from'])->replace(',', '')->toInteger(), Str::of($filters['expected_salary_to'])->replace(',', '')->toInteger()]);
            })
            ->when(!empty($filters['salary_currency']), function ($query) use ($filters) {
                $query->where('salary_currency', $filters['salary_currency']);
            })
            ->when(!empty($filters['availability_type']), function ($query) use ($filters) {
                $types = is_array($filters['availability_type'])
                    ? $filters['availability_type']
                    : [$filters['availability_type']];

                $query->where(function ($q) use ($types) {
                    foreach ($types as $type) {
                        $q->orWhereJsonContains('availability_type', $type instanceof AvailabilityType ? $type->value : $type);
                    }
                });
            })
            ->when(!is_null($filters['availableOnly']), function ($query) use ($filters) {
                $query->where('is_available', $filters['availableOnly']);
            });

        // Get developers by subscription plan
        $premiumDevelopers = (clone $baseQuery)
            ->where('subscription_plan', SubscriptionPlan::PREMIUM)
            ->orderBy('created_at', 'desc')
            ->get();

        $proDevelopers = (clone $baseQuery)
            ->where('subscription_plan', SubscriptionPlan::PRO)
            ->orderBy('created_at', 'desc')
            ->get();

        $freeDevelopers = (clone $baseQuery)
            ->where('subscription_plan', SubscriptionPlan::FREE)
            ->orderBy('created_at', 'desc')
            ->paginate(15);


        $totalCount = $premiumDevelopers->count() + $proDevelopers->count() + $freeDevelopers->total();

        return view('livewire.developer-search', [
            'premiumDevelopers' => $premiumDevelopers,
            'proDevelopers' => $proDevelopers,
            'freeDevelopers' => $freeDevelopers,
            'totalCount' => $totalCount,
        ]);
    }
}
