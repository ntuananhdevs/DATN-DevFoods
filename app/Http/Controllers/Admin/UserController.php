<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('role')
                ->whereHas('role', function ($query) {
                    $query->where('name', 'user');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error in UserController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải danh sách người dùng');
        }
    }

    public function create()
    {
        try {
            $roles = Role::all();  // Get all roles
            return view('admin.users.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Error in UserController@create: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while loading the create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
                'user_name' => 'required|unique:users|min:3|max:255',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|min:8|confirmed',
                'avatar' => 'nullable|image|max:2048',
                'active' => 'boolean'

            ]);

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $validated['avatar'] = $avatarPath;
            }

            $validated['password'] = Hash::make($validated['password']);
            $validated['active'] = $request->has('active');

            User::create($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in UserController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while creating the user.')
                ->withInput();
        }
    }

    public function show(User $user)
    {
        try {
            $user->load('role');
            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error in UserController@show: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while loading user details.');
        }
    }

    public function edit(User $user)
    {
        try {
            $user->load('role');
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error in UserController@edit: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while loading the edit form.');
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
                'user_name' => ['required', 'min:3', 'max:255', Rule::unique('users')->ignore($user->id)],
                'full_name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
                'phone' => 'nullable|string|max:20',
                'password' => 'nullable|min:8|confirmed',
                'avatar' => 'nullable|image|max:2048',
                'active' => 'boolean'
            ]);

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $validated['avatar'] = $avatarPath;
            }

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['active'] = $request->has('active');

            $user->update($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in UserController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while updating the user.')
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in UserController@destroy: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the user.');
        }
    }
}