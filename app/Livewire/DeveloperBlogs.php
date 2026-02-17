<?php

namespace App\Livewire;

use App\Models\DeveloperBlog;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DeveloperBlogs extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $blogs = DeveloperBlog::with(['developer', 'developer.jobTitle'])
            ->published()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('excerpt', 'like', '%'.$this->search.'%')
                        ->orWhereHas('developer', function ($developerQuery) {
                            $developerQuery->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('livewire.developer-blogs', [
            'blogs' => $blogs,
        ]);
    }
}
