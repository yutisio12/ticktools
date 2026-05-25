@extends('layouts.app')

@section('title', 'Create New Ticket')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card overflow-hidden">
        <div class="p-6 border-b border-slate-700/50 bg-slate-800/30">
            <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-6 h-6 text-primary-400"></i>
                Submit a Support Ticket
            </h2>
            <p class="text-sm text-slate-400 mt-1">Please provide as much detail as possible so our IT team can assist you efficiently.</p>
        </div>

        <div class="p-6 md:p-8">
            <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Category & Subcategory -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="label-text">Category <span class="text-red-400">*</span></label>
                        <select id="category_id" name="category_id" required class="input-field" onchange="fetchSubcategories(this.value)">
                            <option value="">Select a Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="subcategory_id" class="label-text">Subcategory</label>
                        <select id="subcategory_id" name="subcategory_id" class="input-field disabled:opacity-50" disabled>
                            <option value="">Select Category first...</option>
                        </select>
                        @error('subcategory_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Asset (Optional) -->
                <div>
                    <label for="asset_id" class="label-text">Related IT Asset (Optional)</label>
                    <select id="asset_id" name="asset_id" class="input-field">
                        <option value="">None / Not Applicable</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_tag }} - {{ $asset->name }} ({{ $asset->type }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Select the device or software this issue relates to, if applicable.</p>
                </div>

                <hr class="border-slate-700">

                <!-- Title -->
                <div>
                    <label for="title" class="label-text">Ticket Title / Subject <span class="text-red-400">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required 
                        placeholder="Brief summary of the issue or request" class="input-field">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="label-text">Detailed Description <span class="text-red-400">*</span></label>
                    <textarea id="description" name="description" rows="6" required
                        placeholder="Please describe your issue in detail. What happened? What were you trying to do? What is the expected outcome?"
                        class="input-field resize-y">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Attachments -->
                <div>
                    <label class="label-text">Attachments (Screenshots, Logs, etc.)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-700 border-dashed rounded-lg bg-slate-900/50 hover:bg-slate-800/50 transition-colors cursor-pointer" onclick="document.getElementById('attachments').click()">
                        <div class="space-y-1 text-center">
                            <i data-lucide="upload-cloud" class="mx-auto h-12 w-12 text-slate-400"></i>
                            <div class="flex text-sm text-slate-400 justify-center">
                                <label for="attachments" class="relative cursor-pointer rounded-md font-medium text-primary-400 hover:text-primary-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                    <span>Upload files</span>
                                    <input id="attachments" name="attachments[]" type="file" multiple class="sr-only" onchange="updateFileList(this)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-500">PNG, JPG, PDF, DOCX up to 10MB each</p>
                        </div>
                    </div>
                    <div id="file-list" class="mt-3 space-y-2 text-sm text-slate-300"></div>
                    @error('attachments.*') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-700">
                    <a href="{{ route('tickets.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary px-8">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fetchSubcategories(categoryId) {
        const subSelect = document.getElementById('subcategory_id');
        
        if (!categoryId) {
            subSelect.innerHTML = '<option value="">Select Category first...</option>';
            subSelect.disabled = true;
            return;
        }

        subSelect.innerHTML = '<option value="">Loading...</option>';
        subSelect.disabled = true;

        fetch(`/api/categories/${categoryId}/subcategories`)
            .then(res => res.json())
            .then(data => {
                subSelect.innerHTML = '<option value="">Select a Subcategory...</option>';
                data.forEach(sub => {
                    subSelect.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
                });
                subSelect.disabled = false;
            })
            .catch(err => {
                console.error(err);
                subSelect.innerHTML = '<option value="">Error loading subcategories</option>';
            });
    }

    function updateFileList(input) {
        const fileList = document.getElementById('file-list');
        fileList.innerHTML = '';
        
        if (input.files.length > 0) {
            const ul = document.createElement('ul');
            ul.className = 'list-disc list-inside';
            Array.from(input.files).forEach(file => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-2 text-primary-300';
                li.innerHTML = `<i data-lucide="paperclip" class="w-4 h-4"></i> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                ul.appendChild(li);
            });
            fileList.appendChild(ul);
            lucide.createIcons();
        }
    }

    // Retain selected subcategory on validation error
    @if(old('category_id'))
        fetchSubcategories({{ old('category_id') }});
        setTimeout(() => {
            const subSelect = document.getElementById('subcategory_id');
            const oldSub = "{{ old('subcategory_id') }}";
            if (oldSub && !subSelect.disabled) {
                subSelect.value = oldSub;
            }
        }, 500);
    @endif
</script>
@endpush
