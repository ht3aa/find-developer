<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RoleCreateRequest;
use App\Http\Requests\Dashboard\RoleDestroyRequest;
use App\Http\Requests\Dashboard\RoleEditRequest;
use App\Http\Requests\Dashboard\RoleIndexRequest;
use App\Http\Requests\Dashboard\StoreRoleRequest;
use App\Http\Requests\Dashboard\UpdateRoleRequest;
use App\Services\PolicyPermissionService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private PolicyPermissionService $policyPermissionService
    ) {}

    public function index(RoleIndexRequest $request): Response
    {
        $roles = Role::withCount('users')->orderBy('name')->get();

        $user = auth()->user();

        return Inertia::render('Roles/Index', [
            'roles' => $roles->map(fn(Role $r) => [
                'id' => $r->id,
                'name' => $r->name,
                'guard_name' => $r->guard_name,
                'users_count' => $r->users_count,
            ]),
            'can' => [
                'updateRole' => $user->can('update', new Role),
                'deleteRole' => $user->can('delete', new Role),
            ],
        ]);
    }

    public function create(RoleCreateRequest $request): Response
    {
        $permissionsByResource = $this->policyPermissionService->getPermissionsGroupedByResource();

        return Inertia::render('Roles/Create', [
            'permissionsByResource' => $permissionsByResource,
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? [];
        unset($data['permission_ids']);

        $role = Role::create($data);
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(RoleEditRequest $request, Role $role): Response
    {
        $role->load('permissions');
        $permissionsByResource = $this->policyPermissionService->getPermissionsGroupedByResource();

        return Inertia::render('Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permission_ids' => $role->permissions->pluck('id')->all(),
            ],
            'permissionsByResource' => $permissionsByResource,
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? [];
        unset($data['permission_ids']);

        $role->update($data);
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(RoleDestroyRequest $request, Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
