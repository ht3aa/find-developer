<?php

namespace App\Http\Middleware;

use App\Models\Badge;
use App\Models\Developer;
use App\Models\DeveloperBlog;
use App\Models\DeveloperCompany;
use App\Models\DeveloperProject;
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
        $isDeveloper = $user->isDeveloper();
        $can = $user ? [
            'viewAnyDeveloper' => $isDeveloper && $user->can('viewAny', Developer::class),
            'viewDeveloperProfile' => $isDeveloper && $user->can('viewDeveloperProfile', Developer::class),
            'viewAnyDeveloperCompany' => $isDeveloper && $user->can('viewAny', DeveloperCompany::class),
            'viewAnyDeveloperProject' => $isDeveloper && $user->can('viewAny', DeveloperProject::class),
            'viewAnyDeveloperBlog' => $isDeveloper && $user->can('viewAny', DeveloperBlog::class),
            'viewAnyBadge' => $user->can('viewAny', Badge::class),
            'viewAnyUser' => $user->can('viewAny', User::class),
            'viewAnyRole' => $user->can('viewAny', Role::class),
            'viewActivityLog' => $user->isSuperAdmin(),
            'viewRecommendationsDashboard' => $user->isSuperAdmin(),
        ] : [];

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
                'permissions' => $permissions,
                'can' => $can,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'info' => fn() => $request->session()->get('info'),
            ],
        ];
    }
}
