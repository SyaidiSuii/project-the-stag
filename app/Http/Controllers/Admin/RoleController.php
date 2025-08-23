<?php

namespace App\Http\Controllers\Admin;
use App\Models\Role;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;
 
 
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        if (request('cancel')) {
            return redirect()->route('admin.role.index');
        }
        // Gate::authorize('access-roles');

        $roles = Role::paginate(10);
        return view('admin.role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = new Role;
        return view('admin.role.create', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Role name is required.',
            'name.unique' => 'Role name already exists.',
        ]);

        $role = new Role;
        $role->fill($request->all());     
        $role->save();

        return redirect()->route('admin.role.index')->with('message', 'Role record has been saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return view('admin.role.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('admin.role.create', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($request->user()->cannot('update', Role::class)) {
            abort(403);
        }

        $this->validate($request, [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Role name is required.',
            'name.unique' => 'Role name already exists.',
        ]);

        $role->fill($request->all());        
        $role->save();

        return redirect()->route('admin.role.index')->with('message', 'Role record has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.role.index')->with('message', 'Role record has been deleted!');
    }
}