<?php

namespace App\Http\Resources;

use App\Models\Developer;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeveloperResource extends JsonResource
{
    /**
     * Transform the resource into an array (shape expected by frontend DeveloperCard).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Developer $developer */
        $developer = $this->resource;

        $location = $developer->location;
        $locationLabel = $location && method_exists($location, 'getLabel')
            ? $location->getLabel()
            : (is_object($location) && property_exists($location, 'value') ? $location->value : null);

        $availabilityType = $developer->availability_type;
        $availabilityTypeArray = is_array($availabilityType)
            ? collect($availabilityType)->map(fn ($t) => [
                'value' => is_object($t) && property_exists($t, 'value') ? $t->value : (string) $t,
                'label' => is_object($t) && method_exists($t, 'getLabel') ? $t->getLabel() : (string) $t,
            ])->values()->all()
            : [];

        $badges = $developer->badges->map(function ($badge) {
            return [
                'name' => $badge->name,
                'color' => $badge->color,
                'icon' => $badge->icon,
            ];
        })->values()->all();

        $recommendations = $developer->relationLoaded('recommendationsReceived')
            ? $developer->recommendationsReceived
                ->map(fn ($r) => [
                    'note' => $r->recommendation_note,
                    'recommender_name' => $r->recommender?->name ?? 'Anonymous',
                    'recommender_job_title' => $r->recommender?->jobTitle?->name ?? null,
                ])
                ->values()
                ->all()
            : [];

        $workExperience = $developer->relationLoaded('companies')
            ? $developer->companies
                ->map(function ($company) {
                    $allRoles = collect([$company])
                        ->concat($company->children ?: collect())
                        ->sortByDesc(fn ($r) => $r->start_date?->timestamp ?? 0)
                        ->values();

                    $roles = $allRoles
                        ->map(fn ($r) => [
                            'job_title' => $r->jobTitle?->name ?? null,
                            'start_date' => $r->start_date?->format('M Y'),
                            'end_date' => $r->is_current ? null : ($r->end_date?->format('M Y') ?? null),
                            'is_current' => $r->is_current,
                            'duration' => $this->formatDuration($r->start_date, $r->end_date, $r->is_current),
                            'description' => $r->description,
                        ])
                        ->values()
                        ->all();

                    $totalDuration = null;
                    if ($allRoles->count() > 1) {
                        $totalMonths = (int) $allRoles->sum(fn ($r) => $r->start_date
                            ? $r->start_date->diffInMonths($r->is_current ? now() : ($r->end_date ?? $r->start_date))
                            : 0);
                        $totalDuration = CarbonInterval::months($totalMonths)->cascade()->forHumans();
                    }

                    return [
                        'company_name' => $company->company_name,
                        'roles' => $roles,
                        'total_duration' => $totalDuration,
                    ];
                })
                ->values()
                ->all()
            : [];

        return [
            'id' => $developer->id,
            'name' => $developer->name,
            'slug' => $developer->slug,
            'email' => $developer->email,
            'years_of_experience' => $developer->years_of_experience,
            'phone' => $developer->phone,
            'expected_salary_from' => $developer->expected_salary_from,
            'expected_salary_to' => $developer->expected_salary_to,
            'currency' => $developer->currency,
            'is_available' => $developer->is_available,
            'bio' => $developer->bio,
            'portfolio_url' => $developer->portfolio_url,
            'github_url' => $developer->github_url,
            'linkedin_url' => $developer->linkedin_url,
            'cv_path_url' => $developer->cv_path_url,
            'recommendations_received_count' => (int) ($developer->recommendations_received_count ?? 0),
            'recommended_by_us' => $developer->recommended_by_us,
            'youtube_video_id' => $developer->getYoutubeVideoId(),
            'badges' => $badges,
            'job_title' => [
                'name' => $developer->jobTitle?->name ?? '',
            ],
            'location' => $locationLabel !== null ? ['label' => $locationLabel] : null,
            'skills' => $developer->skills->map(fn ($s) => ['name' => $s->name])->values()->all(),
            'availability_type' => $availabilityTypeArray,
            'profile_url' => $developer->slug ? url("/developers/{$developer->slug}") : null,
            'badges_page_url' => null,
            'recommendations' => $recommendations,
            'work_experience' => $workExperience,
        ];
    }

    private function formatDuration(?CarbonInterface $start, ?CarbonInterface $end, bool $isCurrent): ?string
    {
        if (! $start) {
            return null;
        }
        $end = $isCurrent ? now() : $end;
        if (! $end) {
            return null;
        }
        $months = $start->diffInMonths($end);
        if ($months < 1) {
            return '< 1 mo';
        }
        $years = (int) floor($months / 12);
        $remainder = $months % 12;
        $yrPart = $years ? $years.' yr'.($years > 1 ? 's' : '') : '';
        $moPart = $remainder ? $remainder.' mo'.($remainder > 1 ? 's' : '') : '';

        return trim($yrPart.($yrPart && $moPart ? ' and ' : '').$moPart) ?: null;
    }
}
