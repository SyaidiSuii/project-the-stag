<?php

namespace App\Http\Controllers\Admin;

  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use App\Models\User;
  use Spatie\Permission\Models\Role;
  use Spatie\Permission\Models\Permission;

  class UserController extends Controller
  {
      public function __construct()
      {
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
              'roles.*' => ['exists:roles,id'],
          ],[
              'name.required' => 'Username is required.',
              'name.min' => 'Username must be at least 5 char.',
          ]);

          $user = User::create([
              'name' => $request->name,
              'email' => $request->email,
              'phone_number' => $request->phone_number,
              'is_active' => $request->is_active ?? true,
              'password' => bcrypt("12345678"),
          ]);

          if ($request->has('roles')) {
              $user->assignRole($request->roles);
          }

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
              'roles.*' => ['exists:roles,id'],
          ],[
              'name.required' => 'Username is required.',
              'name.min' => 'Username must be at least 5 char.',
          ]);

          $user->update([
              'name' => $request->name,
              'email' => $request->email,
              'phone_number' => $request->phone_number,
              'is_active' => $request->is_active ?? true,
          ]);

          $user->syncRoles($request->roles ?? []);

          return redirect()->route('admin.user.index')->with('message', 'User record has been updated!');
      }

      /**
       * Remove the specified resource from storage.
       */
      public function destroy(User $user)
      {
          $user->delete(); // soft delete akan auto detach roles
          return redirect()->route('admin.user.index')->with('message', 'User record has been deleted!');
      }
  }