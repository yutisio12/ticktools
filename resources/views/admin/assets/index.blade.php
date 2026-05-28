@extends('layouts.app')

@section('title', 'Manage Assets')

@section('content')
<div class="glass-card overflow-hidden">
    <div class="p-6 border-b border-slate-700/50 bg-slate-800/30 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
            <i data-lucide="monitor" class="w-6 h-6 text-primary-400"></i>
            IT Assets Inventory
        </h2>
        <button onclick="document.getElementById('create-modal').classList.remove('hidden')" class="btn-primary py-2 px-4 text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Asset
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="text-xs text-slate-400 uppercase bg-slate-800/50">
                <tr>
                    <th scope="col" class="px-6 py-4">Asset Tag</th>
                    <th scope="col" class="px-6 py-4">Name</th>
                    <th scope="col" class="px-6 py-4">Type</th>
                    <th scope="col" class="px-6 py-4">Location</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($assets as $asset)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-white">{{ $asset->asset_tag }}</td>
                        <td class="px-6 py-4">{{ $asset->name }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 bg-slate-800 rounded text-xs">{{ $asset->type }}</span></td>
                        <td class="px-6 py-4">{{ $asset->location ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($asset->is_active)
                                <span class="text-emerald-400 text-xs font-medium">Active</span>
                            @else
                                <span class="text-red-400 text-xs font-medium">Retired</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST" class="inline" onsubmit="return confirm('Delete this asset?');">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-300"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">No assets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-slate-700/50">
        {{ $assets->links() }}
    </div>
</div>

<!-- Create Modal -->
<div id="create-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-md p-6 relative">
            <button onclick="document.getElementById('create-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            <h3 class="text-lg font-semibold text-white mb-4">Add IT Asset</h3>
            
            <form action="{{ route('admin.assets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label-text">Asset Tag <span class="text-red-400">*</span></label>
                        <input type="text" name="asset_tag" required class="input-field" placeholder="e.g. LPT-001">
                    </div>
                    <div>
                        <label class="label-text">Type <span class="text-red-400">*</span></label>
                        <input type="text" name="type" required class="input-field" placeholder="Laptop, PC, Network...">
                    </div>
                </div>
                <div>
                    <label class="label-text">Asset Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required class="input-field" placeholder="e.g. ThinkPad T14 Gen 2">
                </div>
                <div>
                    <label class="label-text">Location / Department</label>
                    <input type="text" name="location" class="input-field">
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary">Save Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
