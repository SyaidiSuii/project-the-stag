<?php

namespace App\Http\Controllers\Admin;

  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use App\Models\User;
  use App\Models\Order;
  use App\Models\KitchenStation;
  use Spatie\Permission\Models\Role;
  use Spatie\Permission\Models\Permission;
  use libphonenumber\PhoneNumberUtil;
  use libphonenumber\PhoneNumberFormat;
  use libphonenumber\NumberParseException;
  use Illuminate\Validation\Rule;

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
      public function index(Request $request)
      {
          if (request('cancel')) {
              return redirect()->route('admin.user.index');
          }

            // Fetch all roles from the database
            $roles = Role::all();

            // Build query with filters
            $query = User::with(['roles', 'assignedStation'])->withCount('orders');

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Role filter
            if ($request->filled('role') && $request->role !== 'all') {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('id', $request->role);
                });
            }

            // Status filter
            if ($request->filled('status') && $request->status !== 'all') {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            // Sorting
            $sortBy = $request->get('sort', 'newest');
            switch ($sortBy) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name':
                    $query->orderBy('name');
                    break;
                case 'email':
                    $query->orderBy('email');
                    break;
                default: // newest
                    $query->latest();
                    break;
            }

            $users = $query->paginate(10)->appends($request->query());

            // Get user statistics
            $totalUsers = User::count();
            $activeCustomers = User::role('customer')
                                  ->where('email_verified_at', '!=', null)
                                  ->count();
            $newRegistrations = User::where('created_at', '>=', now()->subDays(30))
                                   ->count();
            
            // Calculate average orders per customer
            $customersWithOrders = User::role('customer')
                                      ->withCount('orders')
                                      ->having('orders_count', '>', 0)
                                      ->get();
            
            $avgOrdersPerCustomer = $customersWithOrders->count() > 0 
                                   ? round($customersWithOrders->sum('orders_count') / $customersWithOrders->count(), 1)
                                   : 0;

            // Pass data to the view
            return view('admin.user.index', compact(
                'users', 
                'roles', 
                'totalUsers',
                'activeCustomers',
                'newRegistrations',
                'avgOrdersPerCustomer'
            ));
      }

      /**
       * Show the form for creating a new resource.
       */
      public function create()
      {
          $user = new User;
          $roles = Role::all();
          $stations = KitchenStation::where('is_active', true)->orderBy('name')->get();
          return view('admin.user.form', compact('user', 'roles', 'stations'));
      }

      /**
       * Store a newly created resource in storage.
       */
      public function store(Request $request)
      {
          $this->validate($request,[
              'name'=> 'required|min:5',
              'email' => ['required', 'email', Rule::unique('users')->whereNull('deleted_at')],
              'phone_number' => 'nullable|string',
              'password' => 'required|min:8|confirmed',
              'is_active' => 'boolean',
              'roles' => ['nullable', 'array'],
              'roles.*' => ['exists:roles,id'],
              'assigned_station_id' => ['nullable', 'exists:kitchen_stations,id'],
          ]);

          // Phone number processing (only if provided)
          $formattedPhone = null;
          if ($request->filled('phone_number')) {
              $phoneUtil = PhoneNumberUtil::getInstance();

              try {
                  // Parse phone number (default country Malaysia)
                  $numberProto = $phoneUtil->parse($request->phone_number, 'MY');

                  // Validate if it's a valid number
                  if (!$phoneUtil->isValidNumber($numberProto)) {
                      return back()->withErrors(['phone_number' => 'Invalid phone number format']);
                  }

                  // Format to E.164 format (+60123456789)
                  $formattedPhone = $phoneUtil->format($numberProto, PhoneNumberFormat::E164);

              } catch (NumberParseException $e) {
                  return back()->withErrors(['phone_number' => 'Invalid phone number: ' . $e->getMessage()]);
              }
          }

          $user = User::create([
              'name' => $request->name,
              'email' => $request->email,
              'phone_number' => $formattedPhone,
              'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true, // Default to active
              'password' => bcrypt($request->password),
              'assigned_station_id' => $request->assigned_station_id ?: null,
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
          $stations = KitchenStation::where('is_active', true)->orderBy('name')->get();
          $user->load('roles');
          return view('admin.user.form', compact('user', 'roles', 'stations'));
      }

      /**
       * Update the specified resource in storage.
       */
      public function update(Request $request, User $user)
      {
          $this->validate($request,[
              'name'=> 'required|min:5',
              'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')],
              'phone_number' => 'nullable|string',
              'is_active' => 'nullable|boolean',
              'roles' => ['nullable', 'array'],
              'roles.*' => ['exists:roles,id'],
              'assigned_station_id' => ['nullable', 'exists:kitchen_stations,id'],
          ]);

          // Phone number processing (only if provided)
          $formattedPhone = null;
          if ($request->filled('phone_number')) {
              $phoneUtil = PhoneNumberUtil::getInstance();

              try {
                  // Parse phone number (default country Malaysia)
                  $numberProto = $phoneUtil->parse($request->phone_number, 'MY');

                  // Validate if it's a valid number
                  if (!$phoneUtil->isValidNumber($numberProto)) {
                      return back()->withErrors(['phone_number' => 'Invalid phone number format']);
                  }

                  // Format to E.164 format (+60123456789)
                  $formattedPhone = $phoneUtil->format($numberProto, PhoneNumberFormat::E164);

              } catch (NumberParseException $e) {
                  return back()->withErrors(['phone_number' => 'Invalid phone number: ' . $e->getMessage()]);
              }
          }

          $user->update([
              'name' => $request->name,
              'email' => $request->email,
              'phone_number' => $formattedPhone,
              'is_active' => $request->boolean('is_active'), // Menggunakan boolean() lebih aman untuk checkbox
              'assigned_station_id' => $request->assigned_station_id ?: null,
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
                  'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')],
                  'phone_number' => 'nullable|string|max:20',
                  'is_active' => 'nullable|boolean',
              ]);

              // Add phone validation for AJAX updates too
            $formattedPhone = null;
            if ($request->phone_number) {
                $phoneUtil = PhoneNumberUtil::getInstance();
                try {
                    $numberProto = $phoneUtil->parse($request->phone_number, 'MY');
                    if (!$phoneUtil->isValidNumber($numberProto)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid phone number format'
                        ], 422);
                    }
                    $formattedPhone = $phoneUtil->format($numberProto, PhoneNumberFormat::E164);
                } catch (NumberParseException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid phone number: ' . $e->getMessage()
                    ], 422);
                }
            }

              $user->update([
                  'name' => $request->name,
                  'email' => $request->email,
                  'phone_number' => $formattedPhone,
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