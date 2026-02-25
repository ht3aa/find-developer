<?php

namespace App\Http\Resources;

use App\Models\Developer;
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
        ];
    }
}
