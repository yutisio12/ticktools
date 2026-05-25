@extends('layouts.app')

@section('title', 'Tickets')

@section('content')
<div class="glass-card overflow-hidden flex flex-col min-h-[600px]">
    <!-- Header & Filters -->
    <div class="p-6 border-b border-slate-700/50 bg-slate-800/30">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                <i data-lucide="ticket" class="w-6 h-6 text-primary-400"></i>
                All Tickets
            </h2>
            
            <form action="{{ route('tickets.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..." 
                        class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-9 p-2">
                </div>
                
                <select name="status" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2">
                    <option value="">All Statuses</option>
                    @foreach(\App\Enums\TicketStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
                
                <select name="category_id" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white p-2 rounded-lg transition-colors border border-slate-600">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </button>
                
                @if(request()->anyFilled(['search', 'status', 'category_id']))
                    <a href="{{ route('tickets.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Clear</a>
                @endif
            </form>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto flex-1">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="text-xs text-slate-400 uppercase bg-slate-800/50">
                <tr>
                    <th scope="col" class="px-6 py-4">Ticket Info</th>
                    <th scope="col" class="px-6 py-4">Reporter</th>
                    <th scope="col" class="px-6 py-4">Status & Priority</th>
                    <th scope="col" class="px-6 py-4">Assignee</th>
                    <th scope="col" class="px-6 py-4">Created</th>
                    <th scope="col" class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($tickets as $ticket)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-white mb-1">{{ $ticket->ticket_number }}</div>
                            <div class="text-sm text-slate-300 mb-2 truncate max-w-[300px]">{{ $ticket->title }}</div>
                            <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded text-[10px] font-medium bg-{{ $ticket->category->color }}-500/10 text-{{ $ticket->category->color }}-400 border border-{{ $ticket->category->color }}-500/20 uppercase tracking-wider">
                                {{ $ticket->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ $ticket->user->avatar_url }}" alt="" class="w-6 h-6 rounded-full border border-slate-700">
                                <span class="truncate max-w-[120px]">{{ $ticket->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 space-y-2">
                            <div>
                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-{{ $ticket->status->color() }}-500/10 text-{{ $ticket->status->color() }}-400 border border-{{ $ticket->status->color() }}-500/20">
                                    <i data-lucide="{{ $ticket->status->icon() }}" class="w-3.5 h-3.5"></i>
                                    {{ $ticket->status->label() }}
                                </span>
                            </div>
                            @if($ticket->priority)
                                <div>
                                    <span class="inline-flex items-center gap-1 text-{{ $ticket->priority->color() }}-400 text-xs">
                                        <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                                        {{ $ticket->priority->label() }} Priority
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-400">
                            @if($ticket->assignee)
                                <div class="flex items-center gap-2 text-slate-300">
                                    <img src="{{ $ticket->assignee->avatar_url }}" alt="" class="w-6 h-6 rounded-full border border-slate-700">
                                    <span class="truncate max-w-[120px]">{{ $ticket->assignee->name }}</span>
                                </div>
                            @else
                                <span class="text-slate-500 italic">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-400">
                            {{ $ticket->created_at->format('M d, Y') }}<br>
                            {{ $ticket->created_at->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn-secondary py-1.5 px-4 text-sm inline-flex">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <i data-lucide="search-x" class="w-12 h-12 mx-auto mb-4 opacity-50"></i>
                            <p class="text-lg">No tickets found matching your criteria.</p>
                            @if(request()->anyFilled(['search', 'status', 'category_id']))
                                <a href="{{ route('tickets.index') }}" class="text-primary-400 hover:underline mt-2 inline-block">Clear all filters</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="p-4 border-t border-slate-700/50 bg-slate-800/30">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
