<?php

namespace App\Livewire;

use App\Enums\JobStatus;
use App\Enums\Currency;
use App\Enums\WorldGovernorate;
use App\Filament\Customs\ExpectedSalaryFromField;
use App\Filament\Customs\ExpectedSalaryToField;
use App\Models\CompanyJob;
use App\Models\JobTitle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class CompanyJobSearch extends Component implements HasSchemas
{
    use WithPagination;
    use InteractsWithSchemas;

    #[Url]
    public string $search = '';
    #[Url]
    public array $jobTitleIds = [];
    #[Url]
    public array $locationIds = [];
    #[Url]
    public int $salary_from = 0;
    #[Url]
    public int $salary_to = 0;

    #[Url]
    public ?Currency $salary_currency = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filters')
                    ->collapsible()
                    ->description('Filter jobs by various criteria')
                    ->schema([
                        TextInput::make('search')
                            ->label('Search')
                            ->placeholder('Title, company, description...')
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
                                    ->options(JobTitle::active()->limit(50)->pluck('name', 'id'))
                                    ->preload()
                                    ->getSearchResultsUsing(fn(string $query) => JobTitle::active()->where('name', 'like', '%' . $query . '%')->limit(50)->pluck('name', 'id'))
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
                            ]),
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

        $query = CompanyJob::with('jobTitle')
            ->approved()
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('company_name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('requirements', 'like', '%' . $filters['search'] . '%');
                });
            })
            ->when(!empty($filters['jobTitleIds']), function ($query) use ($filters) {
                $query->whereIn('job_title_id', $filters['jobTitleIds']);
            })
            ->when(!empty($filters['locationIds']), function ($query) use ($filters) {
                $query->whereIn('location', $filters['locationIds']);
            })
            ->when(!empty($filters['salary_from']) && $filters['salary_to'] === null, function ($query) use ($filters) {
                $query->where('salary_from', '>=', Str::of($filters['salary_from'])->replace(',', '')->toInteger());
            })
            ->when(!empty($filters['salary_to']) && $filters['salary_from'] === null, function ($query) use ($filters) {
                $query->where('salary_to', '<=', Str::of($filters['salary_to'])->replace(',', '')->toInteger());
            })
            ->when(!empty($filters['salary_from']) && !empty($filters['salary_to']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $from = Str::of($filters['salary_from'])->replace(',', '')->toInteger();
                    $to = Str::of($filters['salary_to'])->replace(',', '')->toInteger();
                    $q->whereBetween('salary_from', [$from, $to])
                        ->orWhereBetween('salary_to', [$from, $to])
                        ->orWhere(function ($subQ) use ($from, $to) {
                            $subQ->where('salary_from', '<=', $from)
                                ->where('salary_to', '>=', $to);
                        });
                });
            })
            ->when(!empty($filters['salary_currency']), function ($query) use ($filters) {
                $query->where('salary_currency', $filters['salary_currency']);
            })
            ->orderBy('created_at', 'desc');

        $jobs = $query->paginate(12);
        $totalCount = $jobs->total();

        return view('livewire.company-job-search', [
            'jobs' => $jobs,
            'totalCount' => $totalCount,
        ]);
    }
}
