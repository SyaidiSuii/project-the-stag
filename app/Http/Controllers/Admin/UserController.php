<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request('cancel')) {
            return redirect()->route('user.index');
        }

        $users = User::paginate(10);
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = new User;
        return view('user.form', compact('user'));
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
        ],[
            'name.required' => 'Username is required.',
            'name.min' => 'Username must be at least 5 char.',
        ]);

        $user = new User;
        $request ['password'] = bcrypt("12345678");
        $user->fill($request->all()); 
        //$user = User::create($request->all()); 
        $user->save();

        return redirect()->route('user.index')->with('message', 'User record has been saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('user.form', compact('user'));
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
        ],[
            'name.required' => 'Username is required.',
            'name.min' => 'Username must be at least 5 char.',
        ]);

        $user->fill($request->all()); 
        $user->save();

        return redirect()->route('user.index')->with('message', 'User record has been update!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('message', 'User record has been delete!');
    }
}