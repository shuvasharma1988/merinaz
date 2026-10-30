<?php

namespace App\Http\Controllers\Backend;

use App\Events\Backend\RoleModified;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreRoleRequest;
use App\Http\Requests\Backend\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Role::class);

        // Load roles with the count of attached permissions and users for the UI matrix
        $roles = Role::withCount(['permissions', 'users'])->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        Gate::authorize('create', Role::class);

        // Group permissions by their domain (e.g., 'bookings', 'staff', 'settings') for the UI grid
        $permissions = auth()->user()->hasRole('owner')
            ? Permission::all()->groupBy('group')
            : auth()->user()->getAllPermissions()->groupBy('group');

        return view('admin.roles.form', compact('permissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create($request->only(['name', 'slug', 'description']));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->validated('permissions'));
        }

        // Dispatch audit event to log the creation and permission assignments
        event(new RoleModified(auth()->user(), 'created', $role));

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    public function edit(Role $role): View
    {
        Gate::authorize('update', $role);

        $permissions = auth()->user()->hasRole('owner')
            ? Permission::all()->groupBy('group')
            : auth()->user()->getAllPermissions()->groupBy('group');

        // Eager load attached permissions to correctly check the boxes in the UI
        $role->load('permissions');

        return view('admin.roles.form', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->only(['name', 'slug', 'description']));

        $role->permissions()->sync($request->validated('permissions', []));

        // Dispatch audit event to log the modification and permission changes
        event(new RoleModified(auth()->user(), 'updated', $role));

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        Gate::authorize('delete', $role);

        $roleName = $role->name;

        // Dispatch audit event BEFORE deletion so the listener has access to the role data
        event(new RoleModified(auth()->user(), 'deleted', $role));

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' has been permanently deleted.");
    }
}
