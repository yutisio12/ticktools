<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('badge_id', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
        }
        
        $users = $query->paginate(15);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'badge_id' => 'required|unique:users,badge_id',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'role_id' => 'required|exists:roles,id',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make(str_replace('-', '', $request->date_of_birth)); // Default password is DOB

        User::create($data);

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'badge_id' => 'required|unique:users,badge_id,' . $user->id,
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'role_id' => 'required|exists:roles,id',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'is_active' => 'boolean',
        ]);

        $user->update($request->all());

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }
        
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
}
