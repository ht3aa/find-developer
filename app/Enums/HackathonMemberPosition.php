<?php

namespace App\Enums;

enum HackathonMemberPosition: string
{
    case TeamLeader = 'team_leader';
    case Developer = 'developer';

    public function label(): string
    {
        return match ($this) {
            self::TeamLeader => 'Team Leader',
            self::Developer => 'Developer',
        };
    }
}
