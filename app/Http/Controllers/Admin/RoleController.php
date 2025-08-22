<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request('cancel')) {
            return redirect()->route('role.index');
        }

        $roles = Role::paginate(10);
        return view('role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = new Role;
        return view('role.form', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Role name is required.',
            'name.unique' => 'Role name already exists.',
        ]);

        $role = new Role;
        $role->fill($request->all());
        
        // Handle permissions as JSON
        if ($request->has('permissions')) {
            $role->permissions = json_encode($request->permissions);
        }
        
        $role->save();

        return redirect()->route('role.index')->with('message', 'Role record has been saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return view('role.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('role.form', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
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
        
        // Handle permissions as JSON
        if ($request->has('permissions')) {
            $role->permissions = json_encode($request->permissions);
        }
        
        $role->save();

        return redirect()->route('role.index')->with('message', 'Role record has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('role.index')->with('message', 'Role record has been deleted!');
    }
}