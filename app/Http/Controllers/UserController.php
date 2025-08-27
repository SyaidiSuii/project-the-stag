<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct()
    {
        // Apply middleware untuk permission checks
        $this->middleware('permission:view-users', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-users', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-users', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check permission using helper method
        if (!auth()->user()->can('view-users')) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'roles' => 'array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Assign roles to user
        if ($request->roles) {
            $user->assignRole($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'array'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update user roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    /**
     * Example method untuk assign role kepada user
     */
    public function assignRole(Request $request, User $user)
    {
        // Check if user has permission to assign roles
        if (!auth()->user()->can('assign-permissions')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user->assignRole($request->role);

        return back()->with('success', 'Role assigned successfully');
    }

    /**
     * Example method untuk remove role dari user
     */
    public function removeRole(Request $request, User $user)
    {
        if (!auth()->user()->can('assign-permissions')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user->removeRole($request->role);

        return back()->with('success', 'Role removed successfully');
    }
}
