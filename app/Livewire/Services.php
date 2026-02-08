<?php

namespace App\Livewire;

use App\Enums\UserType;
use App\Models\Scopes\UserScope;
use App\Models\UserService;
use Livewire\Component;

class Services extends Component
{
    public function render()
    {
        $services = UserService::with(['user', 'appointments', 'badges'])
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
