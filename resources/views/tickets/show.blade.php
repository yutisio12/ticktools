@extends('layouts.app')

@section('title', 'Ticket Details: ' . $ticket->ticket_number)

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    <!-- Main Content: Ticket Details & Timeline -->
    <div class="flex-1 space-y-6">
        
        <!-- Ticket Header & Info -->
        <div class="glass-card overflow-hidden">
            <div class="p-6 border-b border-slate-700/50 flex flex-wrap justify-between items-start gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-bold text-white">{{ $ticket->title }}</h2>
                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-{{ $ticket->status->color() }}-500/10 text-{{ $ticket->status->color() }}-400 border border-{{ $ticket->status->color() }}-500/30 uppercase tracking-wide">
                            <i data-lucide="{{ $ticket->status->icon() }}" class="w-4 h-4"></i>
                            {{ $ticket->status->label() }}
                        </span>
                    </div>
                    <div class="text-slate-400 text-sm flex items-center gap-4">
                        <span class="flex items-center gap-1"><i data-lucide="hash" class="w-4 h-4"></i> {{ $ticket->ticket_number }}</span>
                        <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-4 h-4"></i> {{ $ticket->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>

                @if(Auth::user()->isItStaff() || Auth::user()->isItLead() || Auth::user()->isAdmin())
                    <div class="flex gap-2">
                        @if($ticket->status === \App\Enums\TicketStatus::Open)
                            <form action="{{ route('tickets.claim', $ticket) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary">
                                    <i data-lucide="user-plus" class="w-4 h-4"></i> Claim Ticket
                                </button>
                            </form>
                        @elseif($ticket->status === \App\Enums\TicketStatus::Assigned && $ticket->assigned_to === Auth::id())
                            <form action="{{ route('tickets.start', $ticket) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary">
                                    <i data-lucide="play" class="w-4 h-4"></i> Start Work
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
                
                @if(Auth::user()->isUser() && $ticket->canBeReopened())
                    <button onclick="document.getElementById('reopen-modal').classList.remove('hidden')" class="btn-secondary text-orange-400 hover:text-orange-300 border-orange-500/30">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reopen Ticket
                    </button>
                @endif
            </div>

            <div class="p-6">
                <div class="prose prose-invert max-w-none text-slate-300 mb-8 whitespace-pre-wrap">{{ $ticket->description }}</div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-slate-700/50 pt-6 mt-6">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase mb-1">Reporter</p>
                        <div class="flex items-center gap-2">
                            <img src="{{ $ticket->user->avatar_url }}" class="w-8 h-8 rounded-full border border-slate-700">
                            <div>
                                <p class="text-sm font-semibold text-slate-200">{{ $ticket->user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->user->department ?? 'No Dept' }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase mb-1">Category</p>
                        <p class="text-sm font-semibold text-slate-200">{{ $ticket->category->name }}</p>
                        <p class="text-xs text-slate-400">{{ $ticket->subcategory->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase mb-1">Related Asset</p>
                        @if($ticket->asset)
                            <p class="text-sm font-semibold text-slate-200">{{ $ticket->asset->asset_tag }}</p>
                            <p class="text-xs text-slate-400">{{ $ticket->asset->name }}</p>
                        @else
                            <p class="text-sm text-slate-500 italic">None</p>
                        @endif
                    </div>
                </div>

                <!-- Attachments -->
                @if($ticket->attachments->count() > 0)
                    <div class="mt-6 border-t border-slate-700/50 pt-6">
                        <p class="text-xs font-medium text-slate-500 uppercase mb-3">Attachments</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach($ticket->attachments as $attachment)
                                <a href="{{ $attachment->url }}" target="_blank" class="flex items-center gap-2 p-2 rounded bg-slate-800 border border-slate-700 hover:border-slate-500 transition-colors group">
                                    <div class="p-1.5 bg-slate-700 rounded text-slate-400 group-hover:text-primary-400 transition-colors">
                                        <i data-lucide="file" class="w-4 h-4"></i>
                                    </div>
                                    <div class="text-xs">
                                        <p class="font-medium text-slate-300 truncate max-w-[150px]">{{ $attachment->original_name }}</p>
                                        <p class="text-slate-500">{{ $attachment->human_size }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Resolution & Review details (If closed or pending review) -->
        @if($ticket->resolution)
            <div class="glass-card overflow-hidden border-emerald-500/30">
                <div class="p-4 border-b border-slate-700/50 bg-emerald-950/20 flex items-center gap-2 text-emerald-400 font-semibold">
                    <i data-lucide="check-square" class="w-5 h-5"></i>
                    Resolution Details
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase mb-1">Resolution</p>
                        <div class="bg-slate-900 rounded p-3 text-sm text-slate-300 border border-slate-800">{{ $ticket->resolution }}</div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase mb-1">Root Cause</p>
                        <div class="bg-slate-900 rounded p-3 text-sm text-slate-300 border border-slate-800">{{ $ticket->root_cause }}</div>
                    </div>
                    @if($ticket->prevention)
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase mb-1">Prevention Action</p>
                            <div class="bg-slate-900 rounded p-3 text-sm text-slate-300 border border-slate-800">{{ $ticket->prevention }}</div>
                        </div>
                    @endif
                    
                    @if($ticket->review_notes)
                        <div class="mt-4 pt-4 border-t border-slate-700/50">
                            <p class="text-xs font-medium text-slate-500 uppercase mb-1">Review Notes by {{ $ticket->reviewer->name ?? 'Lead' }}</p>
                            <div class="bg-slate-800/50 rounded p-3 text-sm text-slate-300 border-l-2 border-purple-500">{{ $ticket->review_notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Timeline / Activities -->
        <div class="glass-card overflow-hidden">
            <div class="p-4 border-b border-slate-700/50 bg-slate-800/30 flex justify-between items-center">
                <h3 class="font-semibold text-white flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-accent-400"></i>
                    Activity Timeline
                </h3>
            </div>
            <div class="p-6">
                <div class="relative border-l border-slate-700 ml-3 space-y-8">
                    @foreach($ticket->activities as $activity)
                        <div class="relative pl-6">
                            <!-- Timeline dot -->
                            <div class="absolute w-3 h-3 bg-accent-500 rounded-full -left-1.5 top-1.5 ring-4 ring-slate-900"></div>
                            
                            <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50 hover:border-slate-600 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $activity->user->avatar_url }}" class="w-6 h-6 rounded-full">
                                        <span class="text-sm font-medium text-slate-200">{{ $activity->user->name }}</span>
                                        <span class="text-xs text-slate-500 bg-slate-900 px-2 py-0.5 rounded">Progress: {{ $activity->progress_percentage }}%</span>
                                    </div>
                                    <div class="text-xs text-slate-400 flex flex-col items-end">
                                        <span>{{ $activity->activity_date->format('M d, Y') }}</span>
                                        <span class="text-slate-500 flex items-center gap-1 mt-1"><i data-lucide="clock" class="w-3 h-3"></i> {{ $activity->duration_formatted }}</span>
                                    </div>
                                </div>
                                <p class="text-sm text-slate-300 whitespace-pre-wrap">{{ $activity->activity_note }}</p>
                                
                                @if($activity->attachment_path)
                                    <div class="mt-3">
                                        <a href="{{ asset('storage/' . $activity->attachment_path) }}" target="_blank" class="text-xs text-accent-400 hover:underline flex items-center gap-1">
                                            <i data-lucide="paperclip" class="w-3 h-3"></i> View Attachment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Creation Node -->
                    <div class="relative pl-6">
                        <div class="absolute w-3 h-3 bg-slate-600 rounded-full -left-1.5 top-1.5 ring-4 ring-slate-900"></div>
                        <div class="text-sm text-slate-400 pt-1">
                            Ticket created by <span class="font-medium text-slate-300">{{ $ticket->user->name }}</span>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $ticket->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: SLA, Assignment, Controls -->
    <div class="w-full lg:w-80 space-y-6">
        
        <!-- Assignment & Status Card -->
        <div class="glass-card p-5">
            <h3 class="text-sm font-semibold text-slate-200 mb-4 uppercase tracking-wider">Ticket Info</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-slate-500 mb-1">Assigned To</p>
                    <div class="flex items-center gap-2">
                        @if($ticket->assignee)
                            <img src="{{ $ticket->assignee->avatar_url }}" class="w-8 h-8 rounded-full border border-slate-700">
                            <div>
                                <p class="text-sm font-medium text-slate-200">{{ $ticket->assignee->name }}</p>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500">
                                <i data-lucide="user-x" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm text-slate-400 italic">Unassigned</span>
                        @endif
                    </div>
                </div>

                <hr class="border-slate-700/50">

                <div>
                    <p class="text-xs text-slate-500 mb-1 flex items-center justify-between">
                        Priority
                        @if(Auth::user()->isItStaff() || Auth::user()->isItLead() || Auth::user()->isAdmin())
                            <button onclick="document.getElementById('priority-modal').classList.remove('hidden')" class="text-accent-400 hover:text-accent-300"><i data-lucide="edit-2" class="w-3 h-3"></i></button>
                        @endif
                    </p>
                    @if($ticket->priority)
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-medium bg-{{ $ticket->priority->color() }}-500/10 text-{{ $ticket->priority->color() }}-400 border border-{{ $ticket->priority->color() }}-500/20">
                            <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                            {{ $ticket->priority->label() }}
                        </span>
                    @else
                        <span class="text-sm text-slate-400 italic">Not set</span>
                    @endif
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-1">Impact</p>
                    @if($ticket->impact)
                        <span class="text-sm font-medium text-slate-300">{{ $ticket->impact->label() }}</span>
                    @else
                        <span class="text-sm text-slate-400 italic">Not set</span>
                    @endif
                </div>

                <hr class="border-slate-700/50">

                <!-- SLA Information -->
                <div>
                    <p class="text-xs text-slate-500 mb-2">SLA Deadline</p>
                    @if($ticket->sla_deadline)
                        @php $appSla = app(\App\Services\SlaService::class); @endphp
                        
                        <div class="mb-2">
                            <span class="text-sm font-semibold {{ $ticket->isOverdue() ? 'text-red-400' : 'text-slate-200' }}">
                                {{ $ticket->sla_deadline->format('M d, Y H:i') }}
                            </span>
                            @if($ticket->is_sla_breached)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-red-900/50 text-red-400">BREACHED</span>
                            @endif
                        </div>

                        <!-- Progress Bar -->
                        @if(!in_array($ticket->status, [\App\Enums\TicketStatus::Closed, \App\Enums\TicketStatus::PendingReview]))
                            @php $slaPct = $appSla->getSlaPercentage($ticket->created_at, $ticket->sla_deadline); @endphp
                            <div class="w-full bg-slate-800 rounded-full h-1.5 mb-1">
                                <div class="h-1.5 rounded-full {{ $slaPct > 90 ? 'bg-red-500' : ($slaPct > 75 ? 'bg-orange-500' : 'bg-emerald-500') }}" style="width: {{ $slaPct }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-500">
                                <span>Created</span>
                                <span>{{ $appSla->getRemainingTime($ticket->sla_deadline) }} {{ $ticket->isOverdue() ? 'overdue' : 'left' }}</span>
                            </div>
                        @endif
                    @else
                        <span class="text-sm text-slate-400 italic">Awaiting Priority</span>
                    @endif
                </div>

                @if($ticket->weight_score > 0)
                    <div class="pt-2">
                        <p class="text-xs text-slate-500 flex justify-between items-center">
                            Ticket Weight / Score
                            <span class="font-mono text-xs font-bold text-accent-400">{{ $ticket->weight_score }}</span>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Forms (For IT Staff/Lead) -->
        @if((Auth::user()->isItStaff() || Auth::user()->isItLead() || Auth::user()->isAdmin()) && $ticket->assigned_to === Auth::id())
            
            @if(in_array($ticket->status, [\App\Enums\TicketStatus::InProgress, \App\Enums\TicketStatus::Returned]))
                <!-- Add Activity Form -->
                <div class="glass-card p-5">
                    <h3 class="text-sm font-semibold text-slate-200 mb-4 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> Add Progress
                    </h3>
                    <form action="{{ route('tickets.activities.store', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-sm">
                        @csrf
                        <div>
                            <textarea name="activity_note" rows="3" required placeholder="What did you do?" class="input-field text-sm p-2"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-slate-500 mb-1 block">Progress %</label>
                                <input type="number" name="progress_percentage" min="0" max="100" value="{{ $ticket->latest_progress }}" required class="input-field text-sm p-2">
                            </div>
                            <div>
                                <label class="text-xs text-slate-500 mb-1 block">Duration (min)</label>
                                <input type="number" name="duration_minutes" min="1" required placeholder="e.g. 30" class="input-field text-sm p-2">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-slate-500 mb-1 block">Date</label>
                            <input type="date" name="activity_date" value="{{ date('Y-m-d') }}" required class="input-field text-sm p-2 [color-scheme:dark]">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500 mb-1 block">Attachment (Optional)</label>
                            <input type="file" name="attachment" class="w-full text-xs text-slate-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-slate-300 hover:file:bg-slate-600">
                        </div>
                        <button type="submit" class="w-full btn-secondary py-1.5 mt-2 text-sm">Log Activity</button>
                    </form>
                </div>

                <!-- Submit for Review Form -->
                <div class="glass-card p-5">
                    <h3 class="text-sm font-semibold text-slate-200 mb-4 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Resolve Ticket
                    </h3>
                    <button onclick="document.getElementById('resolve-modal').classList.remove('hidden')" class="w-full btn-primary py-2 text-sm">
                        Submit for Review
                    </button>
                </div>
            @endif
            
            <!-- Update Status (Generic) -->
            <div class="glass-card p-5 border-t border-slate-800">
                <form action="{{ route('tickets.status', $ticket) }}" method="POST" class="flex gap-2">
                    @csrf
                    <select name="status" class="input-field text-sm p-2 flex-1">
                        @foreach(\App\Enums\TicketStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ $ticket->status === $status ? 'selected' : '' }}>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-secondary px-3 py-2"><i data-lucide="save" class="w-4 h-4"></i></button>
                </form>
            </div>
        @endif

        <!-- Lead Review Actions -->
        @if((Auth::user()->isItLead() || Auth::user()->isAdmin()) && $ticket->status === \App\Enums\TicketStatus::PendingReview)
            <div class="glass-card p-5 border-2 border-purple-500/30 bg-purple-950/10">
                <h3 class="text-sm font-semibold text-purple-400 mb-4 uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="eye" class="w-4 h-4"></i> Lead Review
                </h3>
                
                <div class="space-y-3">
                    <button onclick="document.getElementById('approve-modal').classList.remove('hidden')" class="w-full btn-primary bg-emerald-600 hover:bg-emerald-500 py-2 text-sm">
                        <i data-lucide="check" class="w-4 h-4"></i> Approve & Close
                    </button>
                    <button onclick="document.getElementById('return-modal').classList.remove('hidden')" class="w-full btn-secondary text-red-400 hover:text-red-300 border-red-500/30 py-2 text-sm">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Return for Revision
                    </button>
                </div>
            </div>
        @endif
        
    </div>
</div>

<!-- Modals -->

<!-- Priority Modal -->
@if(Auth::user()->isItStaff() || Auth::user()->isItLead() || Auth::user()->isAdmin())
<div id="priority-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-md p-6 relative">
            <button onclick="document.getElementById('priority-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            <h3 class="text-lg font-semibold text-white mb-4">Set Priority & Impact</h3>
            
            <form action="{{ route('tickets.priority', $ticket) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="label-text">Priority Level</label>
                    <select name="priority" required class="input-field">
                        <option value="">Select...</option>
                        @foreach(\App\Enums\Priority::cases() as $priority)
                            <option value="{{ $priority->value }}" {{ $ticket->priority === $priority ? 'selected' : '' }}>{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label-text">Impact Level</label>
                    <select name="impact" required class="input-field">
                        <option value="">Select...</option>
                        @foreach(\App\Enums\Impact::cases() as $impact)
                            <option value="{{ $impact->value }}" {{ $ticket->impact === $impact ? 'selected' : '' }}>{{ $impact->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-3 rounded bg-blue-900/30 text-blue-300 text-xs flex gap-2">
                    <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
                    Setting priority will automatically calculate and set the SLA deadline.
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary">Save & Calculate SLA</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Resolve Modal -->
<div id="resolve-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-lg p-6 relative">
            <button onclick="document.getElementById('resolve-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            <h3 class="text-lg font-semibold text-white mb-4">Submit Resolution</h3>
            
            <form action="{{ route('tickets.submit_review', $ticket) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="label-text">Resolution Actions Taken <span class="text-red-400">*</span></label>
                    <textarea name="resolution" rows="3" required class="input-field" placeholder="Describe exactly what was done to fix the issue"></textarea>
                </div>
                <div>
                    <label class="label-text">Root Cause <span class="text-red-400">*</span></label>
                    <textarea name="root_cause" rows="2" required class="input-field" placeholder="What caused this issue?"></textarea>
                </div>
                <div>
                    <label class="label-text">Prevention Plan (Optional)</label>
                    <textarea name="prevention" rows="2" class="input-field" placeholder="How to prevent this in the future?"></textarea>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary">Submit for Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approve-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-md p-6 relative">
            <button onclick="document.getElementById('approve-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            <h3 class="text-lg font-semibold text-white mb-4">Approve & Close Ticket</h3>
            
            <form action="{{ route('tickets.approve', $ticket) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="label-text">Review Notes (Optional)</label>
                    <textarea name="review_notes" rows="3" class="input-field" placeholder="Any final comments?"></textarea>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary bg-emerald-600 hover:bg-emerald-500">Confirm Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div id="return-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-md p-6 relative">
            <button onclick="document.getElementById('return-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            <h3 class="text-lg font-semibold text-white mb-4">Return for Revision</h3>
            
            <form action="{{ route('tickets.return', $ticket) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="label-text">Reason for Return <span class="text-red-400">*</span></label>
                    <textarea name="review_notes" rows="3" required class="input-field" placeholder="Why is this being returned? What needs to be fixed?"></textarea>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-secondary text-red-400">Return Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reopen Modal -->
<div id="reopen-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-md p-6 relative">
            <button onclick="document.getElementById('reopen-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            <h3 class="text-lg font-semibold text-white mb-4">Reopen Ticket</h3>
            
            <form action="{{ route('tickets.reopen', $ticket) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="label-text">Why are you reopening this? <span class="text-red-400">*</span></label>
                    <textarea name="reason" rows="3" required class="input-field" placeholder="Is the issue still occurring?"></textarea>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-secondary text-orange-400">Confirm Reopen</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
