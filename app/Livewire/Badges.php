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

    public function getTruncatedDescription($badge, $maxLength = 150): array
    {
        if (!$badge->description) {
            return [
                'text' => null,
                'isTruncated' => false,
            ];
        }

        $descLength = strlen($badge->description);
        $isTruncated = $descLength > $maxLength;
        $truncatedText = $isTruncated
            ? substr($badge->description, 0, $maxLength) . '...'
            : $badge->description;

        return [
            'text' => $truncatedText,
            'isTruncated' => $isTruncated,
            'fullText' => $badge->description,
        ];
    }
}
