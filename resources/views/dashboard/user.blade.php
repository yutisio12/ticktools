@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-card p-6 border-t-4 border-t-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400">Total Tickets</p>
                    <h3 class="text-3xl font-bold text-white mt-2">{{ $stats['total'] }}</h3>
                </div>
                <div class="p-3 bg-blue-500/10 rounded-lg">
                    <i data-lucide="ticket" class="w-6 h-6 text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-t-4 border-t-yellow-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400">Open</p>
                    <h3 class="text-3xl font-bold text-white mt-2">{{ $stats['open'] }}</h3>
                </div>
                <div class="p-3 bg-yellow-500/10 rounded-lg">
                    <i data-lucide="circle-dot" class="w-6 h-6 text-yellow-500"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-t-4 border-t-indigo-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400">In Progress</p>
                    <h3 class="text-3xl font-bold text-white mt-2">{{ $stats['in_progress'] }}</h3>
                </div>
                <div class="p-3 bg-indigo-500/10 rounded-lg">
                    <i data-lucide="loader" class="w-6 h-6 text-indigo-500"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-t-4 border-t-emerald-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400">Closed</p>
                    <h3 class="text-3xl font-bold text-white mt-2">{{ $stats['closed'] }}</h3>
                </div>
                <div class="p-3 bg-emerald-500/10 rounded-lg">
                    <i data-lucide="check-circle" class="w-6 h-6 text-emerald-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="glass-card overflow-hidden">
        <div class="p-6 border-b border-slate-700/50 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <i data-lucide="clock" class="w-5 h-5 text-accent-500"></i>
                Recent Tickets
            </h2>
            <a href="{{ route('tickets.index') }}" class="text-sm text-accent-400 hover:text-accent-300 font-medium">View All &rarr;</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs text-slate-400 uppercase bg-slate-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 rounded-tl-lg">Ticket No</th>
                        <th scope="col" class="px-6 py-4">Title</th>
                        <th scope="col" class="px-6 py-4">Category</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Created</th>
                        <th scope="col" class="px-6 py-4 rounded-tr-lg text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($myTickets as $ticket)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-white">{{ $ticket->ticket_number }}</td>
                            <td class="px-6 py-4">{{ Str::limit($ticket->title, 40) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-medium bg-{{ $ticket->category->color }}-500/10 text-{{ $ticket->category->color }}-400 border border-{{ $ticket->category->color }}-500/20">
                                    <i data-lucide="{{ $ticket->category->icon }}" class="w-3.5 h-3.5"></i>
                                    {{ $ticket->category->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-{{ $ticket->status->color() }}-500/10 text-{{ $ticket->status->color() }}-400 border border-{{ $ticket->status->color() }}-500/20">
                                    <i data-lucide="{{ $ticket->status->icon() }}" class="w-3.5 h-3.5"></i>
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">{{ $ticket->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn-secondary py-1.5 px-3 text-xs inline-flex">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-3 opacity-50"></i>
                                <p>No tickets found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
