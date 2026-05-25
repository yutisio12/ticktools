@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Global System Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Users -->
        <div class="glass-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">Total Users</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-white">{{ $stats['total_users'] }}</h3>
                    <span class="text-xs text-emerald-400">({{ $stats['active_users'] }} active)</span>
                </div>
            </div>
            <div class="p-3 bg-blue-500/10 rounded-lg text-blue-500">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>
        
        <!-- Tickets -->
        <div class="glass-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">Total Tickets</p>
                <h3 class="text-2xl font-bold text-white">{{ $stats['total_tickets'] }}</h3>
            </div>
            <div class="p-3 bg-purple-500/10 rounded-lg text-purple-500">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
        </div>
        
        <!-- Active Tickets -->
        <div class="glass-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">Active / Open</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-bold text-white">{{ $stats['open_tickets'] + $stats['in_progress'] }}</h3>
                </div>
            </div>
            <div class="p-3 bg-yellow-500/10 rounded-lg text-yellow-500">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Overdue -->
        <div class="glass-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">System Overdue</p>
                <h3 class="text-2xl font-bold {{ $stats['overdue_tickets'] > 0 ? 'text-red-400' : 'text-emerald-400' }}">{{ $stats['overdue_tickets'] }}</h3>
            </div>
            <div class="p-3 bg-red-500/10 rounded-lg text-red-500">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Charts / Trend area -->
    <div class="glass-card p-6 border border-slate-700/50">
        <h2 class="text-lg font-semibold text-white flex items-center gap-2 mb-6">
            <i data-lucide="bar-chart-2" class="w-5 h-5 text-accent-400"></i>
            Ticket Volume Trend (Last 6 Months)
        </h2>
        
        <div class="h-64 flex items-end gap-4 px-4 pb-8 border-b border-l border-slate-700 relative">
            <!-- Simple CSS Bar Chart for Admin View -->
            @php 
                $maxVolume = collect($monthlyTrend)->max('total') ?: 1;
            @endphp
            
            @foreach($monthlyTrend as $trend)
                @php 
                    $heightPercentage = ($trend->total / $maxVolume) * 100;
                    $monthName = DateTime::createFromFormat('!m', $trend->month)->format('M');
                @endphp
                <div class="flex-1 flex flex-col items-center justify-end h-full group relative">
                    <!-- Tooltip -->
                    <div class="opacity-0 group-hover:opacity-100 absolute -top-10 bg-slate-800 text-white text-xs py-1 px-2 rounded shadow transition-opacity border border-slate-600 whitespace-nowrap z-10 pointer-events-none">
                        {{ $trend->total }} Tickets
                    </div>
                    <!-- Bar -->
                    <div class="w-full max-w-[60px] bg-gradient-to-t from-primary-600 to-accent-500 rounded-t-sm transition-all duration-300 group-hover:brightness-110" style="height: {{ max(5, $heightPercentage) }}%;"></div>
                    <!-- Label -->
                    <div class="absolute -bottom-7 text-xs text-slate-400 font-medium">{{ $monthName }} {{ substr($trend->year, 2) }}</div>
                </div>
            @endforeach
            
            @if(count($monthlyTrend) === 0)
                <div class="absolute inset-0 flex items-center justify-center text-slate-500 text-sm">
                    Not enough data to display trend
                </div>
            @endif
        </div>
    </div>

    <!-- System Recent Activity -->
    <div class="glass-card overflow-hidden">
        <div class="p-5 border-b border-slate-700/50 bg-slate-800/20">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-slate-400"></i>
                Latest System Tickets
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs text-slate-400 uppercase bg-slate-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Ticket / Title</th>
                        <th scope="col" class="px-6 py-3">Reporter</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Assignee</th>
                        <th scope="col" class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-3">
                                <div class="font-medium text-white">{{ $ticket->ticket_number }}</div>
                                <div class="text-xs text-slate-400 truncate max-w-[250px]">{{ $ticket->title }}</div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-700 overflow-hidden">
                                        <img src="{{ $ticket->user->avatar_url }}" alt="">
                                    </div>
                                    <span>{{ $ticket->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-[10px] font-medium bg-{{ $ticket->status->color() }}-500/10 text-{{ $ticket->status->color() }}-400 border border-{{ $ticket->status->color() }}-500/20 uppercase tracking-wider">
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-slate-400">
                                {{ $ticket->assignee->name ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-accent-400 hover:text-accent-300 text-xs font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-slate-500">No recent tickets.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
