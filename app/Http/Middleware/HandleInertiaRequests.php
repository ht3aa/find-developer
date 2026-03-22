<?php

namespace App\Http\Middleware;

use App\Models\Badge;
use App\Models\Conversation;
use App\Models\Developer;
use App\Models\DeveloperBlog;
use App\Models\DeveloperCompany;
use App\Models\DeveloperProject;
use App\Models\Hackathon;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        if ($request->user()?->isSuperAdmin()) {
            $permissions = Permission::all()->pluck('name')->toArray();
        } else {
            $permissions = $request->user()?->getAllPermissions()->pluck('name')->toArray() ?? [];
        }

        $user = $request->user();
        $isDeveloper = $user?->isDeveloper();
        $isAdmin = $user?->isAdmin();
        $can = $user ? [
            'viewAnyDeveloper' => ($isDeveloper || $isAdmin) && $user->can('viewAny', Developer::class),
            'viewDeveloperProfile' => ($isDeveloper || $isAdmin) && $user->can('viewDeveloperProfile', Developer::class),
            'viewAnyDeveloperCompany' => $isDeveloper && $user->can('viewAny', DeveloperCompany::class),
            'viewAnyDeveloperProject' => ($isDeveloper || $isAdmin) && $user->can('viewAny', DeveloperProject::class),
            'viewAnyDeveloperBlog' => ($isDeveloper || $isAdmin) && $user->can('viewAny', DeveloperBlog::class),
            'viewAnyBadge' => $user->can('viewAny', Badge::class),
            'viewAnyHackathon' => $user->can('viewAny', Hackathon::class),
            'viewAnyUser' => $user->can('viewAny', User::class),
            'viewAnyRole' => $user->can('viewAny', Role::class),
            'viewActivityLog' => $user->isSuperAdmin(),
            'viewRecommendationsDashboard' => $user->isSuperAdmin(),
            'viewDeveloperOffers' => $user->isSuperAdmin(),
            'viewNewsletter' => $user->isSuperAdmin(),
            'viewConversations' => $user->isSuperAdmin(),
            'viewDeveloperPhone' => $user->can('viewPhone', Developer::class),
            'viewDeveloperCv' => $user->can('viewCv', Developer::class),
        ] : [];

        $appUrl = rtrim(config('app.url'), '/');
        $ogImage = config('app.og_image');
        if ($ogImage && ! str_starts_with($ogImage, 'http')) {
            $ogImage = $appUrl.($ogImage[0] === '/' ? '' : '/').$ogImage;
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'appUrl' => $appUrl,
            'appDescription' => config('app.description'),
            'appOgImage' => $ogImage,
            'auth' => [
                'user' => $user,
                'permissions' => $permissions,
                'can' => $can,
                'is_super_admin' => $user?->isSuperAdmin() ?? false,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'unreadMessagesCount' => fn () => $user ? $this->getUnreadMessagesCount($user) : 0,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
            ],
        ];
    }

    private function getUnreadMessagesCount(User $user): int
    {
        return $user->conversations()
            ->whereNotNull('last_message_id')
            ->get()
            ->sum(fn (Conversation $c) => $c->unreadCountFor($user->id));
    }
}
