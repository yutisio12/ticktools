@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')
<div class="glass-card overflow-hidden">
    <div class="p-6 border-b border-slate-700/50 bg-slate-800/30 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
            <i data-lucide="layers" class="w-6 h-6 text-primary-400"></i>
            Categories
        </h2>
        <button onclick="document.getElementById('create-modal').classList.remove('hidden')" class="btn-primary py-2 px-4 text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Category
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="text-xs text-slate-400 uppercase bg-slate-800/50">
                <tr>
                    <th scope="col" class="px-6 py-4">Name</th>
                    <th scope="col" class="px-6 py-4">Base Weight</th>
                    <th scope="col" class="px-6 py-4">Subcategories</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($categories as $category)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded bg-{{ $category->color }}-500/20 text-{{ $category->color }}-400">
                                    <i data-lucide="{{ $category->icon ?? 'circle' }}" class="w-4 h-4"></i>
                                </div>
                                <span class="font-medium text-white">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono">{{ $category->base_weight }}</td>
                        <td class="px-6 py-4">{{ $category->subcategories_count }}</td>
                        <td class="px-6 py-4">
                            @if($category->is_active)
                                <span class="text-emerald-400 text-xs font-medium">Active</span>
                            @else
                                <span class="text-red-400 text-xs font-medium">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-300"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No categories found.</td>
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
            <h3 class="text-lg font-semibold text-white mb-4">Add Category</h3>
            
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="label-text">Category Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required class="input-field">
                </div>
                <div>
                    <label class="label-text">Base KPI Weight Score <span class="text-red-400">*</span></label>
                    <input type="number" step="0.1" name="base_weight" value="1.0" required class="input-field">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label-text">Color (Tailwind)</label>
                        <input type="text" name="color" placeholder="blue, red, etc." class="input-field">
                    </div>
                    <div>
                        <label class="label-text">Icon (Lucide)</label>
                        <input type="text" name="icon" placeholder="tag, layers, etc." class="input-field">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
