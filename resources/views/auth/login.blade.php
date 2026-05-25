@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-slate-950">
    <!-- Decorative background elements -->
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-primary-900/30 blur-[100px]"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] rounded-full bg-accent-900/20 blur-[120px]"></div>
    
    <div class="w-full max-w-md relative z-10">
        <!-- Logo/Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-600 to-accent-500 mb-4 shadow-lg shadow-primary-500/30">
                <i data-lucide="life-buoy" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">IT Helpdesk</h1>
            <p class="text-slate-400">Portal Support & Ticketing System</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="p-4 rounded-lg bg-red-950/50 border border-red-800/50 text-red-400 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="badge_id" class="label-text">Badge ID / Employee ID</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="badge" class="w-5 h-5 text-slate-500"></i>
                        </div>
                        <input id="badge_id" type="text" name="badge_id" value="{{ old('badge_id') }}" required autofocus
                            class="input-field pl-10" placeholder="e.g. ADM001">
                    </div>
                </div>

                <div>
                    <label for="date_of_birth" class="label-text">Date of Birth</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="calendar" class="w-5 h-5 text-slate-500"></i>
                        </div>
                        <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                            class="input-field pl-10 [color-scheme:dark]">
                    </div>
                </div>

                <!-- Math Captcha -->
                <div>
                    <label class="label-text">Security Check</label>
                    <div class="flex gap-4">
                        <div id="captcha-container" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg flex items-center justify-center font-mono text-lg font-bold text-accent-400">
                            Loading...
                        </div>
                        <div class="flex-1">
                            <input id="captcha" type="text" name="captcha" required
                                class="input-field text-center" placeholder="Answer">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-600 bg-slate-900 text-primary-600 focus:ring-primary-500 focus:ring-offset-slate-900">
                        <span class="text-sm text-slate-400">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full btn-primary py-3 text-lg">
                    Sign In <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </button>
            </form>
        </div>
        
        <div class="mt-8 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} IT Department. All rights reserved.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch Captcha
        fetch('{{ route('captcha') }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('captcha-container').textContent = data.question;
            });
    });
</script>
@endpush
