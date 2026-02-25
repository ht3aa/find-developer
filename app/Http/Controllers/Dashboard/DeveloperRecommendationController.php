<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\RecommendationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdateDeveloperRecommendationRequest;
use App\Models\DeveloperRecommendation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperRecommendationController extends Controller
{
    /**
     * Super admin only: list all developer recommendations.
     */
    public function index(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $query = DeveloperRecommendation::query()
            ->with(['recommender:id,name,slug', 'recommended:id,name,slug'])
            ->orderByDesc('created_at');

        $status = $request->query('status');
        if (is_string($status) && $status !== '' && RecommendationStatus::tryFrom($status) !== null) {
            $query->where('status', RecommendationStatus::from($status));
        }

        $recommendations = $query->paginate(15)->withQueryString()->through(function (DeveloperRecommendation $r) {
            return [
                'id' => $r->id,
                'recommender_id' => $r->recommender_id,
                'recommender_name' => $r->recommender?->name,
                'recommender_slug' => $r->recommender?->slug,
                'recommended_id' => $r->recommended_id,
                'recommended_name' => $r->recommended?->name,
                'recommended_slug' => $r->recommended?->slug,
                'recommendation_note' => $r->recommendation_note,
                'status' => $r->status->value,
                'status_label' => $r->status->getLabel(),
                'created_at' => $r->created_at->toIso8601String(),
                'updated_at' => $r->updated_at->toIso8601String(),
            ];
        });

        return Inertia::render('DeveloperRecommendations/Index', [
            'recommendations' => $recommendations,
            'filters' => [
                'status' => is_string($status) ? $status : '',
            ],
            'statusOptions' => array_map(
                fn ($case) => ['value' => $case->value, 'label' => $case->getLabel()],
                RecommendationStatus::cases()
            ),
        ]);
    }

    /**
     * Super admin only: show the form for editing a recommendation.
     */
    public function edit(Request $request, DeveloperRecommendation $developer_recommendation): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $developer_recommendation->load(['recommender:id,name,slug', 'recommended:id,name,slug']);

        return Inertia::render('DeveloperRecommendations/Edit', [
            'recommendation' => [
                'id' => $developer_recommendation->id,
                'recommender_name' => $developer_recommendation->recommender?->name,
                'recommender_slug' => $developer_recommendation->recommender?->slug,
                'recommended_name' => $developer_recommendation->recommended?->name,
                'recommended_slug' => $developer_recommendation->recommended?->slug,
                'recommendation_note' => $developer_recommendation->recommendation_note,
                'status' => $developer_recommendation->status->value,
                'created_at' => $developer_recommendation->created_at->toIso8601String(),
                'updated_at' => $developer_recommendation->updated_at->toIso8601String(),
            ],
            'statusOptions' => array_map(
                fn ($case) => ['value' => $case->value, 'label' => $case->getLabel()],
                RecommendationStatus::cases()
            ),
        ]);
    }

    /**
     * Super admin only: update the specified recommendation.
     */
    public function update(UpdateDeveloperRecommendationRequest $request, DeveloperRecommendation $developer_recommendation): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $developer_recommendation->update($request->validated());

        return redirect()
            ->route('developer-recommendations.index')
            ->with('success', 'Recommendation updated successfully.');
    }

    /**
     * Super admin only: remove the specified recommendation.
     */
    public function destroy(Request $request, DeveloperRecommendation $developer_recommendation): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $developer_recommendation->delete();

        return redirect()
            ->route('developer-recommendations.index')
            ->with('success', 'Recommendation deleted successfully.');
    }
}
