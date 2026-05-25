@extends('layouts.app')

@section('title', 'IT Staff Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-card p-6 border-t-4 border-t-yellow-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400">Available Tickets</p>
                    <h3 class="text-3xl font-bold text-white mt-2">{{ $stats['available'] }}</h3>
                </div>
                <div class="p-3 bg-yellow-500/10 rounded-lg">
                    <i data-lucide="inbox" class="w-6 h-6 text-yellow-500"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-t-4 border-t-indigo-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400">My Active Tickets</p>
                    <h3 class="text-3xl font-bold text-white mt-2">{{ $stats['my_active'] }}</h3>
                </div>
                <div class="p-3 bg-indigo-500/10 rounded-lg">
                    <i data-lucide="user-check" class="w-6 h-6 text-indigo-500"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-t-4 border-t-emerald-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400">My Completed</p>
                    <h3 class="text-3xl font-bold text-white mt-2">{{ $stats['my_completed'] }}</h3>
                </div>
                <div class="p-3 bg-emerald-500/10 rounded-lg">
                    <i data-lucide="check-square" class="w-6 h-6 text-emerald-500"></i>
                </div>
            </div>
        </div>

        <div class="glass-card p-6 border-t-4 border-t-red-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-slate-400">My Overdue</p>
                    <h3 class="text-3xl font-bold text-red-400 mt-2">{{ $stats['my_overdue'] }}</h3>
                </div>
                <div class="p-3 bg-red-500/10 rounded-lg">
                    <i data-lucide="alert-circle" class="w-6 h-6 text-red-500"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- My Assigned Tickets -->
        <div class="glass-card overflow-hidden">
            <div class="p-6 border-b border-slate-700/50 flex justify-between items-center bg-slate-800/20">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i data-lucide="briefcase" class="w-5 h-5 text-indigo-400"></i>
                    My Active Work
                </h2>
                <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" class="text-sm text-indigo-400 hover:text-indigo-300 font-medium">View All &rarr;</a>
            </div>
            
            <div class="overflow-x-auto max-h-[400px]">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-800/50 sticky top-0">
                        <tr>
                            <th scope="col" class="px-6 py-4">Ticket</th>
                            <th scope="col" class="px-6 py-4">Priority</th>
                            <th scope="col" class="px-6 py-4">SLA Deadline</th>
                            <th scope="col" class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($myAssignedTickets as $ticket)
                            <tr class="hover:bg-slate-800/30 transition-colors {{ $ticket->is_sla_breached ? 'bg-red-950/20' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white mb-1">{{ $ticket->ticket_number }}</div>
                                    <div class="text-xs text-slate-400 truncate max-w-[200px]">{{ $ticket->title }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($ticket->priority)
                                        <span class="inline-flex items-center gap-1 text-{{ $ticket->priority->color() }}-400">
                                            <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                                            {{ $ticket->priority->label() }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 italic text-xs">Not set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($ticket->sla_deadline)
                                        @php $appSla = app(\App\Services\SlaService::class); @endphp
                                        <div class="{{ $ticket->isOverdue() ? 'text-red-400 font-medium flex items-center gap-1' : 'text-slate-300' }}">
                                            @if($ticket->isOverdue()) <i data-lucide="alert-circle" class="w-4 h-4"></i> @endif
                                            {{ $appSla->getRemainingTime($ticket->sla_deadline) }}
                                        </div>
                                    @else
                                        <span class="text-slate-500 italic text-xs">Pending Priority</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn-secondary py-1.5 px-3 text-xs inline-flex">
                                        Work
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    <p>No active tickets assigned to you.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Available Tickets Pool -->
        <div class="glass-card overflow-hidden">
            <div class="p-6 border-b border-slate-700/50 flex justify-between items-center bg-slate-800/20">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i data-lucide="inbox" class="w-5 h-5 text-yellow-400"></i>
                    Available Pool
                </h2>
                <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="text-sm text-yellow-400 hover:text-yellow-300 font-medium">View All &rarr;</a>
            </div>
            
            <div class="overflow-x-auto max-h-[400px]">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-800/50 sticky top-0">
                        <tr>
                            <th scope="col" class="px-6 py-4">Ticket</th>
                            <th scope="col" class="px-6 py-4">Category</th>
                            <th scope="col" class="px-6 py-4">Created</th>
                            <th scope="col" class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($availableTickets as $ticket)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white mb-1">{{ $ticket->ticket_number }}</div>
                                    <div class="text-xs text-slate-400 truncate max-w-[200px]">{{ $ticket->title }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-[10px] font-medium bg-{{ $ticket->category->color }}-500/10 text-{{ $ticket->category->color }}-400 border border-{{ $ticket->category->color }}-500/20 uppercase">
                                        {{ $ticket->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-400 text-xs">
                                    {{ $ticket->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('tickets.claim', $ticket) }}">
                                        @csrf
                                        <button type="submit" class="bg-primary-600 hover:bg-primary-500 text-white font-medium py-1.5 px-3 rounded text-xs transition-colors shadow-sm">
                                            Claim
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    <p>No available tickets in the pool. Great job!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
