<?php

namespace App\Livewire;

class SpecialNeedsDeveloperSearch extends DeveloperSearch
{
    protected function buildBaseQuery(array $filters)
    {
        return parent::buildBaseQuery($filters)
            ->where('special_needs', true);
    }

    public function render()
    {
        $filters = $this->all();

        return view('livewire.special-needs-developer-search', $this->getViewData($filters));
    }
}
