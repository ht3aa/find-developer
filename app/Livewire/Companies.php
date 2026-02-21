<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Skill;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;
use Livewire\Component;

class Companies extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    #[Url]
    public string $search = '';

    #[Url]
    public array $skills = [];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filters')
                    ->collapsible()
                    ->description('Filter companies by skills they work on')
                    ->schema([
                        Select::make('skills')
                            ->label('Skills')
                            ->multiple()
                            ->searchable()
                            ->options(Skill::active()->limit(50)->pluck('name', 'name'))
                            ->preload()
                            ->getSearchResultsUsing(fn (string $query) => Skill::active()->where('name', 'like', '%' . $query . '%')->limit(50)->pluck('name', 'name'))
                            ->live(),
                    ]),
            ]);
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'skills']);
    }

    public function getActiveFiltersCount(): int
    {
        $count = 0;
        if (trim($this->search) !== '') {
            $count++;
        }
        if (! empty($this->skills)) {
            $count++;
        }

        return $count;
    }

    public function render()
    {
        $companies = $this->buildQuery()->get();

        return view('livewire.companies', [
            'companies' => $companies,
            'activeFiltersCount' => $this->getActiveFiltersCount(),
        ]);
    }

    protected function buildQuery()
    {
        $search = trim($this->search);

        return Company::with('skills')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhereHas('skills', function ($skillQuery) use ($search) {
                            $skillQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when(! empty($this->skills), function ($query) {
                $query->whereHas('skills', function ($skillQuery) {
                    $skillQuery->whereIn('name', $this->skills);
                });
            })
            ->orderBy('name', 'asc');
    }
}
