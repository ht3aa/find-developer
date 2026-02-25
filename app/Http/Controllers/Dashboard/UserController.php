<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreUserRequest;
use App\Http\Requests\Dashboard\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $search = $request->query('search');
        $searchTerm = is_string($search) ? trim($search) : '';

        $query = User::query()
            ->with('roles')
            ->orderBy('name');

        if ($searchTerm !== '') {
            $term = '%'.addcslashes($searchTerm, '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        $users = $query->paginate(15)->withQueryString()->through(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'user_type' => $u->user_type->value,
            'user_type_label' => $u->user_type->getLabel(),
            'can_access_admin_panel' => $u->can_access_admin_panel,
            'roles' => $u->roles->map(fn (Role $r) => ['id' => $r->id, 'name' => $r->name]),
        ]);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => ['search' => $searchTerm],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        $roles = Role::orderBy('name')->get(['id', 'name', 'guard_name']);

        return Inertia::render('Users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $roleIds = $data['role_ids'] ?? [];
        unset($data['role_ids'], $data['password_confirmation']);

        $user = User::create($data);

        if (! empty($roleIds)) {
            $roles = Role::whereIn('id', $roleIds)->get();
            $user->syncRoles($roles);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        $user->load('roles');
        $roles = Role::orderBy('name')->get(['id', 'name', 'guard_name']);

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type->value,
                'can_access_admin_panel' => $user->can_access_admin_panel,
                'linkedin_url' => $user->linkedin_url,
                'role_ids' => $user->roles->pluck('id')->all(),
            ],
            'roles' => $roles,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();
        $roleIds = $data['role_ids'] ?? [];
        unset($data['role_ids'], $data['password_confirmation']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        $roles = empty($roleIds)
            ? []
            : Role::whereIn('id', $roleIds)->get();
        $user->syncRoles($roles);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
