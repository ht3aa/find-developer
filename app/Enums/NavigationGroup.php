<?php

namespace App\Enums;

enum NavigationGroup: string
{
    case Developers = 'Developers';

    case Home = 'Home';

    case Admin = 'System';

    case Others = 'Others';

    public function getLabel(): string
    {
        return $this->value;
    }
}
