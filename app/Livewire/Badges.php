<?php

namespace App\Livewire;

use App\Models\Badge;
use Livewire\Component;

class Badges extends Component
{
    public function render()
    {
        $badges = Badge::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('livewire.badges', [
            'badges' => $badges,
        ]);
    }
}
