<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IT Helpdesk') }} - @yield('title', 'Portal')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-200 min-h-screen flex">
    
    @auth
        <!-- Sidebar Navigation -->
        <nav class="w-64 bg-slate-950 border-r border-slate-800 flex-shrink-0 fixed h-full z-10 transition-transform duration-300">
            <div class="h-16 flex items-center px-6 border-b border-slate-800">
                <div class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-accent-400 to-primary-400 flex items-center gap-2">
                    <i data-lucide="life-buoy" class="text-accent-500"></i>
                    IT Helpdesk
                </div>
            </div>

            <div class="p-4 space-y-6 overflow-y-auto h-[calc(100vh-4rem)]">
                <!-- User Profile Summary -->
                <div class="flex items-center gap-3 px-2 py-3 rounded-lg bg-slate-900/50 border border-slate-800/50">
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full border border-slate-700">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-200 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->role->name }}</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-900/50 text-primary-300' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="pt-4 pb-2">
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Tickets</div>
                    </li>
                    <li>
                        <a href="{{ route('tickets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('tickets.index') ? 'bg-primary-900/50 text-primary-300' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <i data-lucide="ticket" class="w-5 h-5"></i>
                            <span class="font-medium">All Tickets</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tickets.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('tickets.create') ? 'bg-primary-900/50 text-primary-300' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <i data-lucide="plus-circle" class="w-5 h-5"></i>
                            <span class="font-medium">Create Ticket</span>
                        </a>
                    </li>

                    @if(Auth::user()->isAdmin())
                        <li class="pt-4 pb-2">
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Administration</div>
                        </li>
                        <!-- Add admin links here when created -->
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-slate-400 hover:bg-slate-800 hover:text-slate-200">
                                <i data-lucide="users" class="w-5 h-5"></i>
                                <span class="font-medium">Users & Roles</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-slate-400 hover:bg-slate-800 hover:text-slate-200">
                                <i data-lucide="monitor" class="w-5 h-5"></i>
                                <span class="font-medium">Assets</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            
            <div class="absolute bottom-0 w-full p-4 border-t border-slate-800 bg-slate-950">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2 w-full text-left text-slate-400 hover:text-red-400 hover:bg-red-950/30 rounded-lg transition-colors">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        <span class="font-medium">Sign Out</span>
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-1 ml-64 flex flex-col min-h-screen relative">
            <!-- Top Header -->
            <header class="h-16 flex items-center justify-between px-8 bg-slate-900/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-10">
                <h1 class="text-xl font-semibold text-slate-100">@yield('title')</h1>
                
                <div class="flex items-center gap-4">
                    <!-- Notification Bell Component placeholder -->
                    <button class="p-2 text-slate-400 hover:text-slate-200 relative">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-accent-500 rounded-full animate-pulse"></span>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-emerald-950/50 border border-emerald-800/50 text-emerald-400 flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 p-4 rounded-lg bg-red-950/50 border border-red-800/50 text-red-400 flex items-center gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    @else
        <main class="w-full min-h-screen">
            @yield('content')
        </main>
    @endauth

    @livewireScripts
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
