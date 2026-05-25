@extends('layouts.app')

@section('title', 'IT Lead Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- KPI Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="glass-card p-5 border-t-4 border-t-purple-500">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Pending Review</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $stats['pending_review'] }}</h3>
        </div>
        
        <div class="glass-card p-5 border-t-4 border-t-blue-500">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">This Month (Total)</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $stats['total_month'] }}</h3>
        </div>
        
        <div class="glass-card p-5 border-t-4 border-t-emerald-500">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">This Month (Closed)</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ $stats['closed_month'] }}</h3>
        </div>

        <div class="glass-card p-5 border-t-4 border-t-accent-500">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">SLA Compliance</p>
            <h3 class="text-2xl font-bold {{ $stats['sla_compliance'] >= 90 ? 'text-emerald-400' : 'text-yellow-400' }} mt-1">
                {{ $stats['sla_compliance'] }}%
            </h3>
        </div>
        
        <div class="glass-card p-5 border-t-4 border-t-red-500">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Active Overdue</p>
            <h3 class="text-2xl font-bold text-red-400 mt-1">{{ $stats['overdue_active'] }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tickets Pending Review (Takes up 2 columns) -->
        <div class="glass-card overflow-hidden lg:col-span-2 flex flex-col max-h-[600px]">
            <div class="p-5 border-b border-slate-700/50 flex justify-between items-center bg-slate-800/20 shrink-0">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i data-lucide="eye" class="w-5 h-5 text-purple-400"></i>
                    Tickets Pending Review
                </h2>
            </div>
            
            <div class="overflow-y-auto flex-1 p-2">
                @forelse($pendingReview as $ticket)
                    <div class="p-4 mb-2 rounded-lg bg-slate-900/50 border border-slate-700/50 hover:border-slate-600 transition-colors">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-white">{{ $ticket->ticket_number }}</span>
                                    <span class="inline-flex items-center gap-1 text-[10px] uppercase font-bold py-0.5 px-2 rounded bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                        Pending Review
                                    </span>
                                </div>
                                <h4 class="text-slate-300 font-medium">{{ $ticket->title }}</h4>
                            </div>
                            <div class="text-right text-xs">
                                <p class="text-slate-500">Resolved: {{ $ticket->resolved_at->diffForHumans() }}</p>
                                <p class="text-slate-400 mt-1">By: {{ $ticket->assignee->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        
                        <div class="bg-slate-950 rounded p-3 text-sm text-slate-400 mb-3 border border-slate-800">
                            <span class="text-slate-500 font-medium block mb-1">Resolution Notes:</span>
                            {{ Str::limit($ticket->resolution, 120) }}
                        </div>
                        
                        <div class="flex justify-between items-center mt-2">
                            <div class="flex gap-3 text-xs">
                                <span class="flex items-center gap-1 text-slate-500">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    Time: {{ $ticket->resolution_time ?? 'N/A' }}
                                </span>
                                @if($ticket->is_sla_breached)
                                    <span class="flex items-center gap-1 text-red-400 font-medium">
                                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> SLA Breached
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn-primary py-1.5 px-4 text-sm rounded-md shadow-none">
                                Review &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-500 flex flex-col items-center">
                        <i data-lucide="check-circle" class="w-12 h-12 mb-3 text-emerald-500/50"></i>
                        <p class="text-lg font-medium text-slate-300">All caught up!</p>
                        <p class="text-sm mt-1">No tickets pending review.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Team Performance -->
        <div class="glass-card overflow-hidden flex flex-col max-h-[600px]">
            <div class="p-5 border-b border-slate-700/50 bg-slate-800/20 shrink-0">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-accent-400"></i>
                    Team KPI (This Month)
                </h2>
            </div>
            
            <div class="overflow-y-auto flex-1 p-4 space-y-4">
                @foreach($teamMembers as $member)
                    <div class="p-4 rounded-lg bg-slate-900 border border-slate-800">
                        <div class="flex items-center gap-3 mb-3">
                            <img src="{{ $member->avatar_url }}" class="w-10 h-10 rounded-full border border-slate-700">
                            <div>
                                <h4 class="font-medium text-slate-200">{{ $member->name }}</h4>
                                <p class="text-xs text-slate-500">{{ $member->role->name }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-2 text-center mt-3">
                            <div class="bg-slate-800/50 rounded py-2">
                                <div class="text-lg font-bold text-emerald-400">{{ $member->tickets_completed }}</div>
                                <div class="text-[10px] text-slate-500 uppercase">Completed</div>
                            </div>
                            <div class="bg-slate-800/50 rounded py-2">
                                <div class="text-lg font-bold text-blue-400">{{ $member->tickets_active }}</div>
                                <div class="text-[10px] text-slate-500 uppercase">Active</div>
                            </div>
                            <div class="bg-slate-800/50 rounded py-2">
                                <div class="text-lg font-bold {{ $member->tickets_overdue > 0 ? 'text-red-400' : 'text-slate-400' }}">{{ $member->tickets_overdue }}</div>
                                <div class="text-[10px] text-slate-500 uppercase">Overdue</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
