<?php

namespace App\Livewire;

use App\Models\UserService;
use App\Enums\UserType;
use Livewire\Component;
use App\Models\Scopes\UserScope;

class Services extends Component
{
    public function render()
    {
        $services = UserService::with(['user', 'appointments'])
            ->withoutGlobalScopes([UserScope::class])
            ->withCount('appointments')
            ->whereHas('user', function ($query) {
                $query->where('user_type', UserType::HR);
            })
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        return view('livewire.services', [
            'services' => $services,
        ]);
    }
}
