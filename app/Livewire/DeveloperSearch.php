<?php

namespace App\Livewire;

use App\Enums\DeveloperStatus;
use App\Enums\WorldGovernorate;
use App\Enums\SubscriptionPlan;
use App\Models\Developer;
use App\Models\JobTitle;
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

class DeveloperSearch extends Component implements HasSchemas, HasActions
{
    use WithPagination;
    use InteractsWithSchemas;
    use InteractsWithActions;

    public ?array $filterData = [];

    public function mount(): void
    {
        $this->filterData = [
            'search' => '',
            'jobTitleIds' => [],
            'skillIds' => [],
            'locationIds' => [],
            'minExperience' => null,
            'maxExperience' => null,
            'availableOnly' => true,
        ];
    }

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
                                Select::make('jobTitleIds')
                                    ->label('Job Titles')
                                    ->multiple()
                                    ->searchable()
                                    ->options(JobTitle::where('is_active', true)->pluck('name', 'id'))
                                    ->live()
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                Select::make('skillIds')
                                    ->label('Skills')
                                    ->multiple()
                                    ->searchable()
                                    ->options(Skill::where('is_active', true)->pluck('name', 'id'))
                                    ->live()
                                    ->afterStateUpdated(fn() => $this->resetPage()),

                                Select::make('locationIds')
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
                            ]),

                        Checkbox::make('availableOnly')
                            ->label('Available only')
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(fn() => $this->resetPage()),
                    ])
            ])
            ->statePath('filterData');
    }

    public function clearFilters(): void
    {
        $this->filterData = [
            'search' => '',
            'jobTitleIds' => [],
            'skillIds' => [],
            'locationIds' => [],
            'minExperience' => null,
            'maxExperience' => null,
            'availableOnly' => true,
        ];
        $this->resetPage();
    }

    public function render()
    {
        $filters = $this->filterData;

        $baseQuery = Developer::query()
            ->with(['jobTitle', 'skills', 'projects'])
            ->withCount('projects')
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
            ->when(!empty($filters['jobTitleIds']), function ($query) use ($filters) {
                $query->whereIn('job_title_id', $filters['jobTitleIds']);
            })
            ->when(!empty($filters['skillIds']), function ($query) use ($filters) {
                $query->whereHas('skills', function ($skillQuery) use ($filters) {
                    $skillQuery->whereIn('skills.id', $filters['skillIds']);
                });
            })
            ->when(!empty($filters['locationIds']), function ($query) use ($filters) {
                $query->whereIn('location', $filters['locationIds']);
            })
            ->when(!empty($filters['minExperience']), function ($query) use ($filters) {
                $query->where('years_of_experience', '>=', $filters['minExperience']);
            })
            ->when(!empty($filters['maxExperience']), function ($query) use ($filters) {
                $query->where('years_of_experience', '<=', $filters['maxExperience']);
            })
            ->where('is_available', $filters['availableOnly']);

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
            ->paginate(12);

        $totalCount = $premiumDevelopers->count() + $proDevelopers->count() + $freeDevelopers->total();

        return view('livewire.developer-search', [
            'premiumDevelopers' => $premiumDevelopers,
            'proDevelopers' => $proDevelopers,
            'freeDevelopers' => $freeDevelopers,
            'totalCount' => $totalCount,
        ]);
    }
}
