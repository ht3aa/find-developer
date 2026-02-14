<?php

namespace App\Services;

use App\Enums\AvailabilityType;
use App\Enums\Currency;
use App\Enums\WorldGovernorate;
use App\Models\Badge;

class AiPromptBuilder
{
    protected const BASE_SEARCH_URL = 'https://www.find-developer.com';

    protected const HAS_URLS_LABELS = [
        'linkedin_url' => 'LinkedIn URL',
        'github_url' => 'GitHub URL',
        'portfolio_url' => 'Portfolio URL',
    ];

    /**
     * Build prompt text, search URL and hasFilters from a query array (e.g. from DeveloperSearch state).
     *
     * @param  array<string, mixed>  $query
     * @return array{promptText: string, searchUrl: string, hasFilters: bool}
     */
    public static function build(array $query): array
    {
        $lines = self::getRequirementsLines($query);
        $searchUrl = self::getSearchUrl($query);
        $promptText = self::getPromptText($lines, $searchUrl);

        return [
            'promptText' => $promptText,
            'searchUrl' => $searchUrl,
            'hasFilters' => ! empty($lines),
        ];
    }

    public static function getSearchUrl(array $query): string
    {
        $filtered = array_filter($query, function ($v) {
            if (is_array($v)) {
                return $v !== [];
            }
            return $v !== '' && $v !== null;
        });
        if (empty($filtered)) {
            return self::BASE_SEARCH_URL;
        }

        return self::BASE_SEARCH_URL . '/?' . http_build_query($filtered, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<int, string>
     */
    public static function getRequirementsLines(array $query): array
    {
        $lines = [];

        if (! empty($query['search'])) {
            $lines[] = 'Keyword(s): ' . $query['search'];
        }

        $jobTitles = self::normalizeArray($query['jobTitles'] ?? []);
        if (! empty($jobTitles)) {
            $lines[] = 'Job title(s): ' . implode(', ', $jobTitles);
        }

        $skills = self::normalizeArray($query['skills'] ?? []);
        if (! empty($skills)) {
            $lines[] = 'Skills: ' . implode(', ', $skills);
        }

        $locations = self::normalizeArray($query['locations'] ?? []);
        if (! empty($locations)) {
            $labels = array_map(function ($value) {
                try {
                    return WorldGovernorate::from($value)->getLabel();
                } catch (\ValueError) {
                    return $value;
                }
            }, $locations);
            $lines[] = 'Location(s): ' . implode(', ', $labels);
        }

        $minExp = isset($query['minExperience']) ? (int) $query['minExperience'] : 0;
        $maxExp = isset($query['maxExperience']) ? (int) $query['maxExperience'] : 50;
        if ($minExp > 0 || $maxExp < 50) {
            if ($minExp === $maxExp) {
                $lines[] = 'Experience: ' . $minExp . ' year(s)';
            } else {
                $lines[] = 'Experience: ' . $minExp . ' to ' . $maxExp . ' years';
            }
        }

        $from = $query['expected_salary_from'] ?? null;
        $to = $query['expected_salary_to'] ?? null;
        $currency = $query['salary_currency'] ?? null;
        if ((! empty($from) && $from !== '0') || (! empty($to) && $to !== '0')) {
            $currencyLabel = $currency ? (Currency::tryFrom($currency)?->getLabel() ?? $currency) : '';
            $parts = [];
            if (! empty($from) && $from !== '0') {
                $parts[] = 'from ' . $from;
            }
            if (! empty($to) && $to !== '0') {
                $parts[] = 'up to ' . $to;
            }
            $lines[] = 'Expected salary: ' . implode(' ', $parts) . ($currencyLabel ? ' (' . $currencyLabel . ')' : '');
        }

        $availability = self::normalizeArray($query['availability_type'] ?? []);
        if (! empty($availability)) {
            $labels = array_map(function ($value) {
                try {
                    return AvailabilityType::from($value)->getLabel();
                } catch (\ValueError) {
                    return $value;
                }
            }, $availability);
            $lines[] = 'Availability: ' . implode(', ', $labels);
        }

        $hasUrls = self::normalizeArray($query['has_urls'] ?? []);
        if (! empty($hasUrls)) {
            $labels = array_map(fn ($key) => self::HAS_URLS_LABELS[$key] ?? $key, $hasUrls);
            $lines[] = 'Must have: ' . implode(', ', $labels);
        }

        $badgeIds = self::normalizeArray($query['badges'] ?? []);
        if (! empty($badgeIds)) {
            $names = Badge::whereIn('id', $badgeIds)->pluck('name')->all();
            $lines[] = 'Badge(s): ' . implode(', ', $names);
        }

        if (isset($query['availableOnly'])) {
            $lines[] = $query['availableOnly'] == '1' ? 'Available only' : 'Unavailable only';
        }

        return $lines;
    }

    /**
     * @param  array<int, string>  $lines
     */
    private static function getPromptText(array $lines, string $url): string
    {
        $intro = 'Search for developers on https://find-developer.com according to the following company requirements:';
        if (empty($lines)) {
            return $intro . "\n\n(No filters applied – use the link below to browse all developers.)\n\nUse this URL: " . $url;
        }

        $requirements = implode("\n", array_map(fn ($line) => '• ' . $line, $lines));

        return $intro . "\n\n" . $requirements . "\n\nUse this URL: " . $url;
    }

    private static function normalizeArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }
        if (is_string($value) && $value !== '') {
            return [$value];
        }

        return [];
    }
}
