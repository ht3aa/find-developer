<?php

namespace App\Livewire;

use App\Models\ExperienceTask;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ExperienceTasksList extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function clearFilters(): void
    {
        $this->reset();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ExperienceTask::query()
            ->available()
            ->withCount('developers')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc');

        $tasks = $query->paginate(12);
        $totalCount = $tasks->total();

        return view('livewire.experience-tasks-list', [
            'tasks' => $tasks,
            'totalCount' => $totalCount,
        ]);
    }
}
