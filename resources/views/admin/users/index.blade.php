@extends('layouts.app')

@section('title', 'Manage Users & Roles')

@section('content')
<div class="glass-card overflow-hidden">
    <div class="p-6 border-b border-slate-700/50 bg-slate-800/30 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
            <i data-lucide="users" class="w-6 h-6 text-primary-400"></i>
            User Management
        </h2>
        <button onclick="document.getElementById('create-modal').classList.remove('hidden')" class="btn-primary py-2 px-4 text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> New User
        </button>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="text-xs text-slate-400 uppercase bg-slate-800/50">
                <tr>
                    <th scope="col" class="px-6 py-4">User</th>
                    <th scope="col" class="px-6 py-4">Badge ID</th>
                    <th scope="col" class="px-6 py-4">Role & Dept</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full border border-slate-700">
                                <div>
                                    <div class="font-medium text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $user->email ?? 'No email' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-slate-300">{{ $user->badge_id }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center py-0.5 px-2 rounded text-[10px] font-bold bg-primary-900 text-primary-300 border border-primary-700 uppercase tracking-wider mb-1">
                                {{ $user->role->name }}
                            </span>
                            <div class="text-xs text-slate-400">{{ $user->department ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1 text-emerald-400 text-xs font-medium"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Active</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-red-400 text-xs font-medium"><i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <!-- Edit Button triggers modal (simplified for walkthrough) -->
                            <button class="text-accent-400 hover:text-accent-300 mr-3"><i data-lucide="edit" class="w-4 h-4"></i></button>
                            
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete this user?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-300"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-slate-700/50">
        {{ $users->links() }}
    </div>
</div>

<!-- Create Modal -->
<div id="create-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-lg p-6 relative">
            <button onclick="document.getElementById('create-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            <h3 class="text-lg font-semibold text-white mb-4">Create New User</h3>
            
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label-text">Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" required class="input-field">
                    </div>
                    <div>
                        <label class="label-text">Badge ID <span class="text-red-400">*</span></label>
                        <input type="text" name="badge_id" required class="input-field">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label-text">Date of Birth <span class="text-red-400">*</span></label>
                        <input type="date" name="date_of_birth" required class="input-field [color-scheme:dark]">
                    </div>
                    <div>
                        <label class="label-text">Role <span class="text-red-400">*</span></label>
                        <select name="role_id" required class="input-field">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label-text">Department</label>
                        <input type="text" name="department" class="input-field">
                    </div>
                    <div>
                        <label class="label-text">Email</label>
                        <input type="email" name="email" class="input-field">
                    </div>
                </div>
                <div class="p-3 bg-slate-800/50 rounded border border-slate-700 text-xs text-slate-400 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-accent-400"></i>
                    Default password will be Date of Birth (YYYYMMDD format without hyphens).
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
