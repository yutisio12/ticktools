@extends('layouts.app')

@section('title', 'Manage SLA Matrix')

@section('content')
<div class="glass-card overflow-hidden">
    <div class="p-6 border-b border-slate-700/50 bg-slate-800/30 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
            <i data-lucide="clock" class="w-6 h-6 text-primary-400"></i>
            SLA Configuration
        </h2>
        <button onclick="document.getElementById('create-modal').classList.remove('hidden')" class="btn-primary py-2 px-4 text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Add SLA Rule
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="text-xs text-slate-400 uppercase bg-slate-800/50">
                <tr>
                    <th scope="col" class="px-6 py-4">Category</th>
                    <th scope="col" class="px-6 py-4">Priority</th>
                    <th scope="col" class="px-6 py-4">Resolution Time (Hours)</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($slaConfigs as $config)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 font-medium text-white">{{ $config->category->name }}</td>
                        <td class="px-6 py-4">
                            @php $color = $config->priority->color(); @endphp
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-medium
                                @if($color === 'red') bg-red-500/10 text-red-400 border border-red-500/20
                                @elseif($color === 'orange') bg-orange-500/10 text-orange-400 border border-orange-500/20
                                @elseif($color === 'blue') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                @else bg-gray-500/10 text-gray-400 border border-gray-500/20
                                @endif">
                                <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                                {{ $config->priority->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-200">{{ $config->resolution_hours }} hours</td>
                        <td class="px-6 py-4">
                            @if($config->is_active)
                                <span class="text-emerald-400 text-xs font-medium">Active</span>
                            @else
                                <span class="text-red-400 text-xs font-medium">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.sla.destroy', $config) }}" method="POST" class="inline" onsubmit="return confirm('Delete this SLA rule?');">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-300"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No SLA configuration found. Default rules will apply.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="create-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-md p-6 relative">
            <button onclick="document.getElementById('create-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            <h3 class="text-lg font-semibold text-white mb-4">Add SLA Rule</h3>
            
            <form action="{{ route('admin.sla.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="label-text">Category <span class="text-red-400">*</span></label>
                    <select name="category_id" required class="input-field">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label-text">Priority <span class="text-red-400">*</span></label>
                    <select name="priority" required class="input-field">
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label-text">Resolution Time (Hours) <span class="text-red-400">*</span></label>
                    <input type="number" name="resolution_hours" required class="input-field" placeholder="e.g. 4 (for Critical), 24 (for High)">
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary">Save SLA Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
