<?php

namespace App\Livewire;

use App\Services\AiPromptBuilder;
use Livewire\Component;

class AiPrompt extends Component
{
    /** @var string Filter state passed from parent when used as standalone */
    public string $search = '';

    /** @var array<int|string, string> */
    public array $jobTitles = [];

    /** @var array<int|string, string> */
    public array $skills = [];

    /** @var array<int|string, string> */
    public array $locations = [];

    public int $minExperience = 0;

    public int $maxExperience = 50;

    public string $expected_salary_from = '0';

    public string $expected_salary_to = '0';

    /** @var string|null Currency enum value for URL */
    public ?string $salary_currency = null;

    /** @var array<int|string, string> */
    public array $availability_type = [];

    /** @var array<int|string, string> */
    public array $has_urls = [];

    /** @var array<int|string, int|string> */
    public array $badges = [];

    public ?int $availableOnly = null;

    public function getQueryArray(): array
    {
        $query = [
            'search' => $this->search,
            'jobTitles' => $this->jobTitles,
            'skills' => $this->skills,
            'locations' => $this->locations,
            'minExperience' => $this->minExperience,
            'maxExperience' => $this->maxExperience,
            'expected_salary_from' => $this->expected_salary_from,
            'expected_salary_to' => $this->expected_salary_to,
            'availability_type' => $this->availability_type,
            'has_urls' => $this->has_urls,
            'badges' => $this->badges,
        ];
        if ($this->salary_currency !== null && $this->salary_currency !== '') {
            $query['salary_currency'] = $this->salary_currency;
        }
        if ($this->availableOnly !== null) {
            $query['availableOnly'] = $this->availableOnly;
        }

        return $query;
    }

    public function render()
    {
        $data = AiPromptBuilder::build($this->getQueryArray());

        return view('livewire.ai-prompt', [
            'promptText' => $data['promptText'],
            'searchUrl' => $data['searchUrl'],
            'hasFilters' => $data['hasFilters'],
        ]);
    }
}
