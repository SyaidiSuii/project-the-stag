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

            // 2. Fetch all roles from the database
            $roles = Role::all();

            // Fetch users with roles and orders count
            $users = User::with('roles')
                        ->withCount('orders')
                        ->latest()
                        ->paginate(10);

            // 3. Pass both $users and $roles to the view
            return view('admin.user.index', compact('users', 'roles'));
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
              'email'=>'required|email|unique:users,email,NULL,id,deleted_at,NULL',
              'phone_number' => 'required|string|max:20',
              'is_active' => 'boolean',
              'roles' => ['nullable', 'array'],
              'roles.*' => ['exists:roles,id'],
          ]);

          // SARAN: Hindari hardcoded password. Pertimbangkan untuk membuat password acak
          // dan mengirimkannya ke email pengguna atau membuat alur pengaturan password terpisah.
          // Contoh: 'password' => bcrypt(Str::random(10)),
          $user = User::create([
              'name' => $request->name,
              'email' => $request->email,
              'phone_number' => $request->phone_number,
              'is_active' => $request->boolean('is_active'), // Menggunakan boolean() lebih aman untuk checkbox
              'password' => bcrypt("12345678"),
          ]);

          // Convert role IDs to role objects with explicit guard checking
          if ($request->has('roles') && is_array($request->roles)) {
              $roleIds = array_filter($request->roles); // Remove empty values
              $validRoles = Role::whereIn('id', $roleIds)->where('guard_name', 'web')->get();
              $user->syncRoles($validRoles);
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
              'email'=>'required|email|unique:users,email,'.$user->id.',id,deleted_at,NULL',
              'phone_number' => 'required|string|max:20',
              'is_active' => 'nullable|boolean',
              'roles' => ['nullable', 'array'],
              'roles.*' => ['exists:roles,id'],
          ]);

          $user->update([
              'name' => $request->name,
              'email' => $request->email,
              'phone_number' => $request->phone_number,
              'is_active' => $request->boolean('is_active'), // Menggunakan boolean() lebih aman untuk checkbox
          ]);

          // Convert role IDs to role objects with explicit guard checking
          if ($request->has('roles') && is_array($request->roles)) {
              $roleIds = array_filter($request->roles); // Remove empty values
              $validRoles = Role::whereIn('id', $roleIds)->where('guard_name', 'web')->get();
              $user->syncRoles($validRoles);
          } else {
              // If no roles are selected, remove all roles
              $user->syncRoles([]);
          }

          return redirect()->route('admin.user.index')->with('message', 'User record has been updated!');
      }

      /**
       * Remove the specified resource from storage.
       */
      public function destroy(User $user)
      {
          // PERHATIAN: Soft delete TIDAK otomatis detach roles. Relasi tetap ada.
          // Jika Anda ingin menghapus relasi role saat user di-soft delete, lakukan secara eksplisit.
          // $user->roles()->detach();

          $user->delete();
          return redirect()->route('admin.user.index')->with('message', 'User record has been deleted!');
      }

      /**
       * Get user data for AJAX modal edit
       */
      public function getEditData(User $user)
      {
          $user->load(['roles']);
          $user->loadCount('orders');
          
          return response()->json([
              'success' => true,
              'user' => $user
          ]);
      }

      /**
       * Update user via AJAX
       */
      public function updateAjax(Request $request, User $user)
      {
          try {
              $this->validate($request,[
                  'name'=> 'required|min:3',
                  'email'=>'required|email|unique:users,email,'.$user->id.',id,deleted_at,NULL',
                  'phone_number' => 'nullable|string|max:20',
                  'is_active' => 'nullable|boolean',
              ]);

              $user->update([
                  'name' => $request->name,
                  'email' => $request->email,
                  'phone_number' => $request->phone_number,
                  'is_active' => $request->boolean('is_active'),
              ]);

              return response()->json([
                  'success' => true,
                  'message' => 'User updated successfully',
                  'user' => $user
              ]);
          } catch (\Illuminate\Validation\ValidationException $e) {
              return response()->json([
                  'success' => false,
                  'message' => 'Validation failed',
                  'errors' => $e->errors()
              ], 422);
          } catch (\Exception $e) {
              return response()->json([
                  'success' => false,
                  'message' => 'An error occurred: ' . $e->getMessage()
              ], 500);
          }
      }
  }