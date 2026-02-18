<!DOCTYPE html>
<html lang="en" x-data="{ theme: localStorage.getItem('theme') || 'dark' }" :class="{ 'dark': theme === 'dark' }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generation Team - Admin Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/gt_logo.jpg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;500;600;700&display=swap"
        rel="stylesheet">


    <style>
        :root {
            /* Light Mode (Safe/Clean) */
            --bg-main: #f3f4f6;
            --bg-header: rgba(255, 255, 255, 0.95);
            --bg-sidebar: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --primary: #175c53;
            /* Dark Teal/Green */
            --primary-hover: #114a42;
            --accent: #D4AF37;
            /* Gold */
            --border: #e5e7eb;
            /* Gray 200 */
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --glow: 0 0 0 transparent;
            --grid-color: #d1d5db;
        }

        [data-theme="dark"] {
            /* Dark Mode (Refined Slate - Softer & High Contrast) */
            --bg-main: #0f172a;       /* Slate 900 - Deep Blue-Grey */
            --bg-header: rgba(15, 23, 42, 0.95); 
            --bg-sidebar: #0f172a;    /* Slate 900 */
            --text-main: #f8fafc;     /* Slate 50 - Bright White */
            --text-muted: #94a3b8;    /* Slate 400 */
            --primary: #06b6d4;       /* Cyan 500 */
            --primary-hover: #0891b2; /* Cyan 600 */
            --accent: #f59e0b;        /* Amber 500 - Gold/Yellow */
            --border: #334155;        /* Slate 700 */
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            --glow: 0 0 15px rgba(245, 158, 11, 0.15); /* Subtle Amber Glow */
            --grid-color: #334155;
        }

        /* 
           🔥 Global Overrides for Legacy Views 🔥 
           Forces hardcoded Tailwind classes to adapt to Dark Mode
        */
        [data-theme="dark"] .bg-white { background-color: #1e293b !important; color: var(--text-main) !important; border-color: var(--border) !important; }
        [data-theme="dark"] .bg-gray-50, [data-theme="dark"] .bg-gray-100 { background-color: var(--bg-main) !important; }
        [data-theme="dark"] .text-gray-900, [data-theme="dark"] .text-gray-800, [data-theme="dark"] .text-black { color: var(--text-main) !important; }
        [data-theme="dark"] .text-gray-700, [data-theme="dark"] .text-gray-600, [data-theme="dark"] .text-gray-500 { color: var(--text-muted) !important; }
        [data-theme="dark"] .border-gray-100, [data-theme="dark"] .border-gray-200, [data-theme="dark"] .border-gray-300 { border-color: var(--border) !important; }
        [data-theme="dark"] input, [data-theme="dark"] select, [data-theme="dark"] textarea { 
            background-color: var(--bg-main) !important; 
            color: var(--text-main) !important; 
            border-color: var(--border) !important; 
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        /* Responsive Images */
        img, video {
            max-width: 100%;
            height: auto;
        }

        h1,
        h2,
        h3 {
            font-family: 'Orbitron', sans-serif;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-main);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--text-muted);
            border-radius: 4px;
            opacity: 0.5;
        }

        /* Sidebar Links */
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-muted);
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link:hover {
            background-color: rgba(0, 243, 255, 0.05);
            color: var(--primary);
            box-shadow: var(--shadow);
            transform: translateX(4px);
        }

        /* Light mode fix */
        :not([data-theme="dark"]) .sidebar-link:hover {
            background-color: #f9fafb;
            color: var(--primary);
            box-shadow: none;
        }

        .sidebar-link.active {
            background-color: rgba(0, 243, 255, 0.1);
            color: var(--primary);
            border-left: 3px solid var(--primary);
            box-shadow: var(--glow);
        }

        :not([data-theme="dark"]) .sidebar-link.active {
            background-color: #f0fdf9;
            /* Light teal bg */
            color: var(--primary);
            border-left: 3px solid var(--primary);
            box-shadow: none;
        }

        /* Glass Panels */
        .glass-panel {
            background: var(--bg-sidebar);
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
    </style>
</head>

<body class="antialiased" :data-theme="theme">

    <div class="min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false }">

        {{-- Header --}}
        <header
            class="h-16 flex items-center justify-between px-6 z-50 fixed w-full top-0 border-b transition-colors duration-300"
            style="background-color: var(--bg-header); border-color: var(--border); backdrop-filter: blur(10px);">

            {{-- Mobile Button --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden mr-4 transition-colors focus:outline-none" style="color: var(--text-muted)">
                <i class="fas fa-bars text-2xl"></i>
            </button>

            <div class="flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-lg overflow-hidden border transition-all duration-300 shadow-md group-hover:scale-105"
                    style="border-color: var(--primary)">
                    <img src="{{ asset('assets/gt_logo.jpg') }}" class="w-full h-full object-cover">
                </div>
                <h1 class="text-lg md:text-xl font-bold tracking-widest uppercase transition-colors duration-300"
                    style="color: var(--text-main)">
                    GEN<span class="hidden xs:inline">ERATION</span> <span style="color: var(--primary)">TEAM</span> <span
                        class="text-[10px] tracking-normal hidden sm:block normal-case font-thin"
                        style="color: var(--text-muted)">Admin Portal</span>
                </h1>
            </div>

            <div class="flex items-center gap-4">

                <!-- Theme Toggle -->
                <button @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', theme)"
                    class="hidden md:flex w-8 h-8 rounded-full items-center justify-center transition-all duration-300 focus:outline-none border hover:shadow-lg"
                    style="background-color: var(--bg-sidebar); border-color: var(--border); color: var(--text-muted)">
                    <i class="fas fa-sun text-yellow-500 text-sm" x-show="theme === 'light'" style="display: none;"></i>
                    <i class="fas fa-moon text-cyan-400 text-sm" x-show="theme === 'dark'" style="display: none;"></i>
                </button>

                {{-- Notifications --}}
                <div class="relative ml-auto sm:mr-6" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 transition-colors hover:scale-110"
                        style="color: var(--text-muted)">
                        <i class="fas fa-bell text-xl hover:text-[var(--primary)] transition-colors"></i>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center w-2 h-2 rounded-full animate-ping"
                                style="background-color: var(--accent)"></span>
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold leading-none text-black rounded-full shadow-sm"
                                style="background-color: var(--accent); color: #000">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    {{-- Notification Dropdown --}}
                    <div x-show="open" @click.away="open = false" style="display: none;"
                        class="absolute right-0 mt-2 w-80 sm:w-96 rounded-xl shadow-2xl overflow-hidden z-50 border"
                        style="background-color: var(--bg-sidebar); border-color: var(--border)">

                        <div class="px-4 py-3 border-b flex justify-between items-center"
                            style="border-color: var(--border); background-color: var(--bg-main)">
                            <span class="text-sm font-bold" style="color: var(--text-main)">Notifications</span>
                            <a href="{{ route('notifications.readAll') }}" class="text-xs hover:underline font-medium"
                                style="color: var(--primary)">Mark all read</a>
                        </div>
                        <div class="max-h-80 overflow-y-auto custom-scroll">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <div class="p-4 border-b transition-colors {{ $notification->read_at ? 'opacity-60' : '' }}"
                                    style="border-color: var(--border); background-color: {{ $notification->read_at ? 'transparent' : 'rgba(0, 243, 255, 0.05)' }}">
                                    <div class="flex gap-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center border"
                                                style="background-color: var(--bg-main); border-color: var(--border)">
                                                <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} text-sm"
                                                    style="color: var(--text-muted)"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold" style="color: var(--text-main)">
                                                {{ $notification->data['title'] }}</p>
                                            <p class="text-xs mt-1 leading-relaxed" style="color: var(--text-muted)">
                                                {{ $notification->data['body'] }}</p>
                                            <p class="text-[10px] mt-2 opacity-70" style="color: var(--text-muted)">
                                                {{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-sm" style="color: var(--text-muted)">
                                    <i class="far fa-bell-slash mb-2 text-lg"></i>
                                    <p>No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User Profile --}}
                <div class="flex items-center gap-3 border-l pl-6" style="border-color: var(--border)">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold leading-tight font-tech" style="color: var(--text-main)">Leader
                            {{ auth()->user()->name }}</p>
                        <p class="text-[9px] uppercase tracking-widest font-bold" style="color: var(--accent)">Team
                            Admin</p>
                    </div>
                    <div class="w-10 h-10 rounded-full p-0.5 border shadow-lg overflow-hidden transition-all hover:scale-110"
                        style="border-color: var(--accent); box-shadow: var(--glow)">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=000&color=D4AF37&bold=true"
                            class="w-full h-full rounded-full object-cover">
                    </div>
                </div>
            </div>
        </header>

        <div class="flex flex-1 pt-16">
            {{-- Mobile Overlay --}}
            <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" style="display: none;"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm z-40 md:hidden">
            </div>

            {{-- Sidebar --}}
            <aside
                class="w-64 border-r flex flex-col fixed h-full pb-16 overflow-y-auto z-50 transition-transform duration-300 transform md:translate-x-0 glass-panel"
                style="border-color: var(--border)" :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'">

                <nav class="flex-1 py-8 space-y-2 px-3">

                    {{-- 1. Dashboard Link --}}
                    <a href="{{ route('staff.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home text-lg"></i>
                        <span class="font-medium text-sm ml-3">Dashboard</span>
                    </a>

                    {{-- 2. Projects Main Menu --}}
                    <div x-data="{
                        projectsOpen: {{ request()->routeIs('staff.proposals') || request()->routeIs('staff.my_teams') || request()->routeIs('staff.team.manage') || request()->routeIs('staff.grading') || request()->routeIs('subjects.*') || request()->routeIs('staff.doctor_timetable') ? 'true' : 'false' }},
                        gradOpen: {{ request()->routeIs('staff.proposals') || request()->routeIs('staff.my_teams') || request()->routeIs('staff.team.manage') || request()->routeIs('staff.grading') ? 'true' : 'false' }}
                    }" class="mt-2">

                        <button @click="projectsOpen = !projectsOpen"
                            class="flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all hover:bg-opacity-10 group"
                            style="color: {{ request()->routeIs('staff.*') && !request()->routeIs('staff.dashboard') ? 'var(--primary)' : 'var(--text-muted)' }}"
                            :style="projectsOpen ? 'background-color: rgba(0,243,255,0.05)' : ''">
                            <div class="flex items-center">
                                <i
                                    class="fas fa-folder-open w-6 text-lg group-hover:text-[var(--primary)] transition-colors"></i>
                                <span class="text-sm ml-3 font-medium">Projects</span>
                            </div>
                            <i class="fas fa-chevron-right text-[10px] transition-transform duration-300"
                                :class="{ 'rotate-90': projectsOpen }"></i>
                        </button>

                        <div x-show="projectsOpen" x-collapse class="space-y-1 overflow-hidden">

                            {{-- A. Graduation Projects Sub-Menu --}}
                            <div class="mt-1">
                                <button @click="gradOpen = !gradOpen"
                                    class="flex items-center justify-between w-full pl-8 pr-4 py-2 rounded-lg transition-all hover:bg-opacity-10"
                                    style="color: var(--text-muted)">
                                    <div class="flex items-center">
                                        <i class="fas fa-graduation-cap w-5 text-sm"></i>
                                        <span class="text-xs font-bold ml-2">Generation Team Projects</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-[8px] transition-transform duration-300"
                                        :class="{ 'rotate-90': gradOpen }"></i>
                                </button>

                                <div x-show="gradOpen" x-collapse class="pl-10 mt-1 space-y-1 border-l-2 ml-6"
                                    style="border-color: var(--border)">

                                    {{-- Proposals --}}
                                    @if (auth()->user()->hasPermission('view_proposals'))
                                        <a href="{{ route('staff.proposals') }}"
                                            class="flex items-center px-4 py-2 text-xs rounded-r-lg transition"
                                            style="color: {{ request()->routeIs('staff.proposals') ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: {{ request()->routeIs('staff.proposals') ? 'bold' : 'normal' }}">
                                            <span class="w-1.5 h-1.5 rounded-full mr-2"
                                                style="background-color: {{ request()->routeIs('staff.proposals') ? 'var(--primary)' : 'var(--text-muted)' }}"></span>
                                            Proposals Review
                                        </a>
                                    @endif

                                    {{-- My Teams --}}
                                    @if (auth()->user()->hasPermission('manage_teams'))
                                        <a href="{{ route('staff.my_teams') }}"
                                            class="flex items-center px-4 py-2 text-xs rounded-r-lg transition"
                                            style="color: {{ request()->routeIs('staff.my_teams') || request()->routeIs('staff.team.manage') ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: {{ request()->routeIs('staff.my_teams') || request()->routeIs('staff.team.manage') ? 'bold' : 'normal' }}">
                                            <span class="w-1.5 h-1.5 rounded-full mr-2"
                                                style="background-color: {{ request()->routeIs('staff.my_teams') || request()->routeIs('staff.team.manage') ? 'var(--primary)' : 'var(--text-muted)' }}"></span>
                                            My Teams
                                        </a>
                                    @endif

                                </div>
                            </div>

                            {{-- B. Subject Projects --}}
                            @if (auth()->user()->hasPermission('manage_subjects'))
                                <a href="{{ route('subjects.index') }}"
                                    class="flex items-center pl-8 pr-4 py-2 rounded-lg transition-all hover:text-[var(--primary)]"
                                    style="color: var(--text-muted)">
                                    <i class="fas fa-book w-5 text-sm"></i>
                                    <span class="text-xs font-bold ml-2">Subject Projects</span>
                                </a>
                            @endif

                            {{-- C. Defense Timetable --}}
                            @if (auth()->user()->hasPermission('view_defense'))
                                <a href="{{ route('staff.doctor_timetable') }}"
                                    class="flex items-center pl-8 pr-4 py-2 rounded-lg transition-all hover:text-[var(--primary)]"
                                    style="color: var(--text-muted)">
                                    <i class="fas fa-table w-5 text-sm"></i>
                                    <span class="text-xs font-bold ml-2">Defense Timetable</span>
                                </a>
                            @endif

                        </div>
                    </div>

                    {{-- User Management --}}
                    @if (
                            auth()->user()->role === 'admin' ||
                            auth()->user()->hasPermission('manage_users') ||
                            auth()->user()->hasPermission('view_activity_log') ||
                            auth()->user()->hasPermission('manage_teams_db')
                        )

                        <div x-data="{
                                userMgmtOpen: {{ request()->routeIs('admin.users*') || request()->routeIs('admin.activity_logs') || request()->routeIs('admin.teams.*') ? 'true' : 'false' }}
                            }" class="mt-2">

                            <button @click="userMgmtOpen = !userMgmtOpen"
                                class="flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all hover:bg-opacity-10 group"
                                style="color: {{ request()->routeIs('admin.users*') ? 'var(--primary)' : 'var(--text-muted)' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-users-cog w-6 text-lg group-hover:text-[var(--primary)]"></i>
                                    <span class="text-sm ml-3">User Management</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] transition-transform duration-300"
                                    :class="{ 'rotate-90': userMgmtOpen }"></i>
                            </button>

                            <div x-show="userMgmtOpen" x-collapse class="space-y-1 overflow-hidden">

                                {{-- Users List --}}
                                @if (auth()->user()->role === 'admin' || auth()->user()->hasPermission('manage_users'))
                                    <a href="{{ route('admin.users') }}"
                                        class="flex items-center pl-8 pr-4 py-2 rounded-lg transition-all hover:text-[var(--primary)]"
                                        style="color: {{ request()->routeIs('admin.users') ? 'var(--primary)' : 'var(--text-muted)' }}">
                                        <i class="fas fa-users w-5 text-sm"></i>
                                        <span class="text-xs font-bold ml-2">All Users List</span>
                                    </a>
                                @endif

                                {{-- Activity Logs --}}
                                @if (auth()->user()->role === 'admin' || auth()->user()->hasPermission('view_activity_log'))
                                    <a href="{{ route('admin.activity_logs') }}"
                                        class="flex items-center pl-8 pr-4 py-2 rounded-lg transition-all hover:text-[var(--primary)]"
                                        style="color: {{ request()->routeIs('admin.activity_logs') ? 'var(--primary)' : 'var(--text-muted)' }}">
                                        <i class="fas fa-shield-alt w-5 text-sm"></i>
                                        <span class="text-xs font-bold ml-2">Activity Logs</span>
                                    </a>
                                @endif

                                {{-- Teams Database --}}
                                @if (auth()->user()->role === 'admin' || auth()->user()->hasPermission('manage_teams_db'))
                                    <a href="{{ route('admin.teams.index') }}"
                                        class="flex items-center pl-8 pr-4 py-2 rounded-lg transition-all hover:text-[var(--primary)]"
                                        style="color: {{ request()->routeIs('admin.teams.*') ? 'var(--primary)' : 'var(--text-muted)' }}">
                                        <i class="fas fa-network-wired w-5 text-sm"></i>
                                        <span class="text-xs font-bold ml-2">Teams Database</span>
                                    </a>
                                @endif

                            </div>
                        </div>
                    @endif

                    <div class="my-6 border-t mx-4" style="border-color: var(--border)"></div>

                    <div class="mt-auto px-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center justify-center gap-2 text-red-500 hover:bg-red-500/10 font-bold text-sm w-full py-3 rounded-xl transition border border-transparent hover:border-red-500/50">
                                <i class="fas fa-power-off"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            <main class="flex-1 md:ml-64 p-4 md:p-8 relative transition-colors duration-300">
                {{-- Background Grid Effect for Main Content --}}
                <div class="absolute inset-0 z-0 pointer-events-none opacity-20"
                    style="background-image: radial-gradient(var(--grid-color) 1px, transparent 1px); background-size: 20px 20px;">
                </div>

                <div class="relative z-10">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>