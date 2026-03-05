<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdateDeveloperOfferRequest;
use App\Models\DeveloperOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperOfferController extends Controller
{
    /**
     * Super admin only: list all developer offers.
     */
    public function index(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $query = DeveloperOffer::query()
            ->with(['jobTitle:id,name', 'user:id,name,email'])
            ->orderByDesc('created_at');

        $status = $request->query('status');
        if (is_string($status) && $status !== '' && OfferStatus::tryFrom($status) !== null) {
            $query->where('status', OfferStatus::from($status));
        }

        $offers = $query->paginate(15)->withQueryString()->through(function (DeveloperOffer $o) {
            $developers = $o->developers();

            return [
                'id' => $o->id,
                'developer_ids' => $o->developer_ids,
                'developer_names' => $developers->pluck('name')->toArray(),
                'company_name' => $o->company_name,
                'job_title_name' => $o->jobTitle?->name,
                'message' => $o->message,
                'salary_range' => $o->salary_range,
                'work_type' => $o->work_type?->getLabel(),
                'contact_email' => $o->contact_email,
                'status' => $o->status->value,
                'status_label' => $o->status->getLabel(),
                'created_at' => $o->created_at->toIso8601String(),
                'updated_at' => $o->updated_at->toIso8601String(),
            ];
        });

        return Inertia::render('DeveloperOffers/Index', [
            'offers' => $offers,
            'filters' => [
                'status' => is_string($status) ? $status : '',
            ],
            'statusOptions' => array_map(
                fn ($case) => ['value' => $case->value, 'label' => $case->getLabel()],
                OfferStatus::cases()
            ),
        ]);
    }

    /**
     * Super admin only: show offer details.
     */
    public function show(Request $request, DeveloperOffer $developer_offer): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $developer_offer->load(['jobTitle:id,name', 'user:id,name,email']);
        $developers = $developer_offer->developers();

        $offer = [
            'id' => $developer_offer->id,
            'developer_ids' => $developer_offer->developer_ids,
            'developers' => $developers->map(fn ($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'slug' => $d->slug,
                'email' => $d->email,
            ])->toArray(),
            'company_name' => $developer_offer->company_name,
            'job_title_name' => $developer_offer->jobTitle?->name,
            'message' => $developer_offer->message,
            'salary_range' => $developer_offer->salary_range,
            'work_type' => $developer_offer->work_type?->getLabel(),
            'contact_email' => $developer_offer->contact_email,
            'status' => $developer_offer->status->value,
            'status_label' => $developer_offer->status->getLabel(),
            'created_at' => $developer_offer->created_at->toIso8601String(),
            'updated_at' => $developer_offer->updated_at->toIso8601String(),
            'submitted_by' => $developer_offer->user?->name,
            'submitted_by_email' => $developer_offer->user?->email,
        ];

        return Inertia::render('DeveloperOffers/Show', [
            'offer' => $offer,
            'statusOptions' => array_map(
                fn ($case) => ['value' => $case->value, 'label' => $case->getLabel()],
                OfferStatus::cases()
            ),
        ]);
    }

    /**
     * Super admin only: update offer status.
     */
    public function update(UpdateDeveloperOfferRequest $request, DeveloperOffer $developer_offer): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $developer_offer->update($request->validated());

        return redirect()
            ->route('developer-offers.index')
            ->with('success', 'Offer status updated successfully.');
    }

    /**
     * Super admin only: delete the offer.
     */
    public function destroy(Request $request, DeveloperOffer $developer_offer): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $developer_offer->delete();

        return redirect()
            ->route('developer-offers.index')
            ->with('success', 'Offer deleted successfully.');
    }
}
