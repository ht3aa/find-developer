<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\BulkEmailAllNewsletterRequest;
use App\Http\Requests\Dashboard\BulkEmailNewsletterRequest;
use App\Http\Requests\Dashboard\NewsletterDestroyRequest;
use App\Models\Newsletter;
use App\Notifications\MailtrapNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscribers.
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $searchTerm = is_string($search) ? trim($search) : '';

        $query = Newsletter::query()->orderByDesc('created_at');

        if ($searchTerm !== '') {
            $term = '%'.addcslashes($searchTerm, '%_\\').'%';
            $query->where('email', 'like', $term);
        }

        $subscribers = $query
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Newsletter $n) => [
                'id' => $n->id,
                'email' => $n->email,
                'subscribed_at' => $n->created_at->toIso8601String(),
                'delete_url' => route('dashboard.newsletter.destroy', $n),
            ]);

        return Inertia::render('Newsletter/Index', [
            'subscribers' => $subscribers,
            'filters' => [
                'search' => $searchTerm,
            ],
            'bulkEmailUrl' => route('dashboard.newsletter.bulk-email'),
            'bulkEmailAllUrl' => route('dashboard.newsletter.bulk-email-all'),
        ]);
    }

    /**
     * Send Mailtrap email to selected newsletter subscribers.
     */
    public function bulkEmail(BulkEmailNewsletterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $subscriberIds = $validated['subscriber_ids'];
        $title = $validated['title'];
        $body = $validated['body'];
        $category = $validated['category'] ?? null;

        $subscribers = Newsletter::query()
            ->whereIn('id', $subscriberIds)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($subscribers->isEmpty()) {
            return redirect()
                ->route('dashboard.newsletter.index')
                ->with('error', 'No valid subscriber emails to send to.');
        }

        $message = "Hello,\n\nYou are receiving this message from the ".config('app.name')." platform.\n\n---\n\n"
            .trim($body)
            ."\n\n---\n\nBest Regards,\nThe team at ".config('app.url');

        foreach ($subscribers as $subscriber) {
            $subscriber->notify(new MailtrapNotification($title, $message, $category));
        }

        return redirect()
            ->route('dashboard.newsletter.index')
            ->with('success', 'Bulk email sent to '.$subscribers->count().' subscriber(s) via Mailtrap.');
    }

    /**
     * Send Mailtrap email to all newsletter subscribers.
     */
    public function bulkEmailAll(BulkEmailAllNewsletterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $title = $validated['title'];
        $body = $validated['body'];
        $category = $validated['category'] ?? null;

        $subscribers = Newsletter::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('email')
            ->get();

        if ($subscribers->isEmpty()) {
            return redirect()
                ->route('dashboard.newsletter.index')
                ->with('error', 'No valid subscriber emails to send to.');
        }

        $message = "Hello,\n\nYou are receiving this message from the ".config('app.name')." platform.\n\n---\n\n"
            .trim($body)
            ."\n\n---\n\nBest Regards,\nThe team at ".config('app.url');

        foreach ($subscribers as $subscriber) {
            $subscriber->notify(new MailtrapNotification($title, $message, $category));
        }

        return redirect()
            ->route('dashboard.newsletter.index')
            ->with('success', 'Bulk email sent to '.$subscribers->count().' subscriber(s) via Mailtrap.');
    }

    /**
     * Remove a newsletter subscriber.
     */
    public function destroy(NewsletterDestroyRequest $request, Newsletter $newsletter): RedirectResponse
    {
        $newsletter->delete();

        return redirect()
            ->route('dashboard.newsletter.index')
            ->with('success', 'Subscriber removed successfully.');
    }
}
