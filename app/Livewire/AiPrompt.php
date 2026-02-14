<?php

namespace App\Livewire;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\WorldGovernorate;
use App\Models\Badge;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

class AiPrompt extends Component
{
    protected const BASE_SEARCH_URL = 'https://www.find-developer.com';

    protected const HAS_URLS_LABELS = [
        'linkedin_url' => 'LinkedIn URL',
        'github_url' => 'GitHub URL',
        'portfolio_url' => 'Portfolio URL',
    ];

    public function getSearchUrl(): string
    {
        $query = Request::query();
        if (empty($query)) {
            return self::BASE_SEARCH_URL;
        }

        return self::BASE_SEARCH_URL.'/?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    public function getRequirementsLines(): array
    {
        $query = Request::query();
        $lines = [];

        if (! empty($query['search'])) {
            $lines[] = 'Keyword(s): '.$query['search'];
        }

        $jobTitles = $this->normalizeArray($query['jobTitles'] ?? []);
        if (! empty($jobTitles)) {
            $lines[] = 'Job title(s): '.implode(', ', $jobTitles);
        }

        $skills = $this->normalizeArray($query['skills'] ?? []);
        if (! empty($skills)) {
            $lines[] = 'Skills: '.implode(', ', $skills);
        }

        $locations = $this->normalizeArray($query['locations'] ?? []);
        if (! empty($locations)) {
            $labels = array_map(function ($value) {
                try {
                    return WorldGovernorate::from($value)->getLabel();
                } catch (\ValueError) {
                    return $value;
                }
            }, $locations);
            $lines[] = 'Location(s): '.implode(', ', $labels);
        }

        $minExp = isset($query['minExperience']) ? (int) $query['minExperience'] : 0;
        $maxExp = isset($query['maxExperience']) ? (int) $query['maxExperience'] : 50;
        if ($minExp > 0 || $maxExp < 50) {
            if ($minExp === $maxExp) {
                $lines[] = 'Experience: '.$minExp.' year(s)';
            } else {
                $lines[] = 'Experience: '.$minExp.' to '.$maxExp.' years';
            }
        }

        $from = $query['expected_salary_from'] ?? null;
        $to = $query['expected_salary_to'] ?? null;
        $currency = $query['salary_currency'] ?? null;
        if ((! empty($from) && $from !== '0') || (! empty($to) && $to !== '0')) {
            $currencyLabel = $currency ? (Currency::tryFrom($currency)?->getLabel() ?? $currency) : '';
            $parts = [];
            if (! empty($from) && $from !== '0') {
                $parts[] = 'from '.$from;
            }
            if (! empty($to) && $to !== '0') {
                $parts[] = 'up to '.$to;
            }
            $lines[] = 'Expected salary: '.implode(' ', $parts).($currencyLabel ? ' ('.$currencyLabel.')' : '');
        }

        $availability = $this->normalizeArray($query['availability_type'] ?? []);
        if (! empty($availability)) {
            $labels = array_map(function ($value) {
                try {
                    return AvailabilityType::from($value)->getLabel();
                } catch (\ValueError) {
                    return $value;
                }
            }, $availability);
            $lines[] = 'Availability: '.implode(', ', $labels);
        }

        $hasUrls = $this->normalizeArray($query['has_urls'] ?? []);
        if (! empty($hasUrls)) {
            $labels = array_map(fn ($key) => self::HAS_URLS_LABELS[$key] ?? $key, $hasUrls);
            $lines[] = 'Must have: '.implode(', ', $labels);
        }

        $badgeIds = $this->normalizeArray($query['badges'] ?? []);
        if (! empty($badgeIds)) {
            $names = Badge::whereIn('id', $badgeIds)->pluck('name')->all();
            $lines[] = 'Badge(s): '.implode(', ', $names);
        }

        if (isset($query['availableOnly'])) {
            $lines[] = $query['availableOnly'] == '1' ? 'Available only' : 'Unavailable only';
        }

        return $lines;
    }

    public function getPromptText(): string
    {
        $lines = $this->getRequirementsLines();
        $url = $this->getSearchUrl();

        $intro = 'Search for developers on https://find-developer.com according to the following company requirements:';
        if (empty($lines)) {
            return $intro."\n\n(No filters applied – use the link below to browse all developers.)\n\nUse this URL: ".$url;
        }

        $requirements = implode("\n", array_map(fn ($line) => '• '.$line, $lines));

        return $intro."\n\n".$requirements."\n\nUse this URL: ".$url;
    }

    private function normalizeArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }
        if (is_string($value) && $value !== '') {
            return [$value];
        }

        return [];
    }

    public function render()
    {
        return view('livewire.ai-prompt', [
            'promptText' => $this->getPromptText(),
            'searchUrl' => $this->getSearchUrl(),
            'requirementsLines' => $this->getRequirementsLines(),
            'hasFilters' => ! empty($this->getRequirementsLines()),
        ]);
    }
}
