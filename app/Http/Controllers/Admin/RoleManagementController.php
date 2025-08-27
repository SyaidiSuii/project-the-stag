<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-roles', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-roles', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-roles', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-roles', ['only' => ['destroy']]);
        $this->middleware('permission:assign-roles', ['only' => ['assignToUser', 'removeFromUser']]);
    }

    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        // Assign permissions to role
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->givePermissionTo($permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully!');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $permissions = $role->permissions;
        $users = $role->users;
        return view('admin.roles.show', compact('role', 'permissions', 'users'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->update([
            'name' => $request->name
        ]);

        // Sync permissions
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of core roles
        if (in_array($role->name, ['admin', 'manager', 'user'])) {
            return back()->with('error', 'Cannot delete core system roles.');
        }

        // Check if role is being used
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that is currently assigned to users.');
        }

        $role->delete();
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully!');
    }

    /**
     * Show form to assign roles to users
     */
    public function assignForm()
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        return view('admin.roles.assign', compact('users', 'roles'));
    }

    /**
     * Assign role to user
     */
    public function assignToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user = User::findOrFail($request->user_id);
        
        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $user->syncRoles($roles);
        } else {
            $user->syncRoles([]);
        }

        return back()->with('success', 'Roles assigned to user successfully!');
    }

    /**
     * Get role permissions via AJAX
     */
    public function getRolePermissions(Role $role)
    {
        $permissions = $role->permissions->pluck('name');
        return response()->json($permissions);
    }
}
