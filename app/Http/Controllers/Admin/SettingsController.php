<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class SettingsController extends Controller
{
    /**
     * Display settings page with admin management section.
     */
    public function index()
    {
        // Get all users with admin or manager role
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'manager']);
        })->with('roles')->latest()->get();

        $currentUser = auth()->user();

        return view('admin.settings.index', compact('admins', 'currentUser'));
    }

    /**
     * Store a newly created admin.
     * Only super admin can create new admins.
     */
    public function storeAdmin(Request $request)
    {
        // Check if user is super admin
        if (!auth()->user()->is_super_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can create admin accounts.'
            ], 403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->whereNull('deleted_at')],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'role' => ['required', 'in:admin,manager'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Assign role
        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully!',
            'admin' => $user->load('roles')
        ]);
    }

    /**
     * Update admin information.
     * Super admin can edit all, regular admin can only edit themselves.
     */
    public function updateAdmin(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        // Check if the user is actually an admin/manager
        if (!$admin->hasAnyRole(['admin', 'manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'User is not an admin.'
            ], 404);
        }

        // Check permissions
        $currentUser = auth()->user();

        // Regular admin can only edit themselves
        if (!$currentUser->is_super_admin && $currentUser->id !== $admin->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own profile.'
            ], 403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)->whereNull('deleted_at')],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'role' => ['required', 'in:admin,manager'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Update user
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
        ];

        // Only super admin can change active status and role
        if ($currentUser->is_super_admin) {
            $updateData['is_active'] = $request->has('is_active') ? true : false;

            // Update role if changed
            if ($admin->getRoleNames()->first() !== $request->role) {
                $admin->syncRoles([$request->role]);
            }
        }

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Admin updated successfully!',
            'admin' => $admin->fresh()->load('roles')
        ]);
    }

    /**
     * Delete admin.
     * Only super admin can delete admins.
     */
    public function deleteAdmin($id)
    {
        // Check if user is super admin
        if (!auth()->user()->is_super_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can delete admin accounts.'
            ], 403);
        }

        $admin = User::findOrFail($id);

        // Check if the user is actually an admin/manager
        if (!$admin->hasAnyRole(['admin', 'manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'User is not an admin.'
            ], 404);
        }

        // Prevent deleting yourself
        if ($admin->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 400);
        }

        // Soft delete the user
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin deleted successfully!'
        ]);
    }

    /**
     * Toggle super admin status.
     * Only existing super admin can promote/demote.
     */
    public function toggleSuperAdmin($id)
    {
        // Check if user is super admin
        if (!auth()->user()->is_super_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can change super admin status.'
            ], 403);
        }

        $admin = User::findOrFail($id);

        // Check if the user is actually an admin/manager
        if (!$admin->hasAnyRole(['admin', 'manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'User is not an admin.'
            ], 404);
        }

        // Prevent demoting yourself
        if ($admin->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own super admin status.'
            ], 400);
        }

        // Toggle super admin status
        $admin->update([
            'is_super_admin' => !$admin->is_super_admin
        ]);

        $status = $admin->is_super_admin ? 'promoted to' : 'demoted from';

        return response()->json([
            'success' => true,
            'message' => "Admin {$status} Super Admin successfully!",
            'admin' => $admin->fresh()->load('roles')
        ]);
    }
}
