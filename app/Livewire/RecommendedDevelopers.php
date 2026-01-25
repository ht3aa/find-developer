<?php

namespace App\Livewire;

use App\Enums\RecommendationStatus;
use App\Models\Developer;
use App\Models\Scopes\DeveloperScope;
use Livewire\Component;
use Livewire\WithPagination;

class RecommendedDevelopers extends Component
{
    use WithPagination;

    public function render()
    {
        $developers = Developer::with(['jobTitle', 'skills'])
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
            ->recommended()
            ->orderBy('recommendations_received_count', 'desc')
            ->paginate(15);

        return view('livewire.recommended-developers', [
            'developers' => $developers,
        ]);
    }
}
