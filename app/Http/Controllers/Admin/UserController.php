<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request('cancel')) {
            return redirect()->route('admin.user.index');
        }

        $users = User::with('roles')->latest()->paginate(10);
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = new User;
        $roles = Role::all();
        $user->load('roles');
        return view('admin.user.form', compact('user', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=> 'required|min:5',
            'email'=>'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'is_active' => 'boolean',
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'], // semak ID role wujud dalam table roles
        ],[
            'name.required' => 'Username is required.',
            'name.min' => 'Username must be at least 5 char.',
        ]);

        $user = new User;
        $request ['password'] = bcrypt("12345678");
        $user->fill($request->all()); 
        //$user = User::create($request->all()); 

        // hanya laksanakan operasi sekira ada input
        if ($request->has('roles')) {
            $user->roles()->attach($request->roles);
        }

        $user->save();

        return redirect()->route('admin.user.index')->with('message', 'User record has been saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.user.form', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request,[
            'name'=> 'required|min:5',
            'email'=>'required|email|unique:users,email,'.$user->id,
            'phone_number' => 'required|string|max:20',
            'is_active' => 'boolean',
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'], // pastikan ID ada dalam table roles
        ],[
            'name.required' => 'Username is required.',
            'name.min' => 'Username must be at least 5 char.',
        ]);

        $user->fill($request->all());
        
        // laksanakan operasi atau kosongkan 
        $user->roles()->sync($request->roles ?? []);

        $user->save();

        return redirect()->route('admin.user.index')->with('message', 'User record has been update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // hapuskan rekod roles
        $user->roles()->detach();
        $user->delete();
        return redirect()->route('admin.user.index')->with('message', 'User record has been delete!');
    }
}