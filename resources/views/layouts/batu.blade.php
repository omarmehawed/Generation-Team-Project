<!DOCTYPE html>
<html lang="en" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{ 'dark': theme === 'dark' }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generation Team - System</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/gt_logo.jpg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;500;600;700&family=Figtree:wght@300;400;500;600;700&family=Amiri:wght@400;700&display=swap"
        rel="stylesheet">


    <style>
        :root {
            /* Light Mode (Ramadan Morning) */
            --bg-main: #f8fafc;
            --bg-panel: #ffffff;
            --bg-sidebar: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --primary: #1e1b4b;
            /* Ramadan Night */
            --primary-hover: #312e81;
            --accent: #fbbf24;
            /* Gold */
            --border: #e2e8f0;
            --shadow: 0 4px 20px -2px rgba(30, 27, 75, 0.1);
            --glow: 0 0 15px rgba(251, 191, 36, 0.2);
            /* Gold Glow */
            --nav-bg: rgba(255, 255, 255, 0.90);
            --grid-color: #e2e8f0;
        }

        [data-theme="dark"] {
            /* Dark Mode (Ramadan Night) */
            --bg-main: #0f172a;
            --bg-panel: #1e1b4b;
            /* Deep Indigo Panel */
            --bg-sidebar: #0f172a;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #fbbf24;
            /* Gold */
            --primary-hover: #d97706;
            --accent: #818cf8;
            /* Soft Indigo */
            --border: #312e81;
            /* Indigo Border */
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
            --glow: 0 0 20px rgba(251, 191, 36, 0.15);
            --nav-bg: rgba(15, 23, 42, 0.90);
            --grid-color: #1e293b;
        }

        /* 
           🔥 Global Overrides for Legacy Views 🔥 
           Forces hardcoded Tailwind classes to adapt to Dark Mode
        */
        [data-theme="dark"] .bg-white {
            background-color: var(--bg-panel) !important;
            color: var(--text-main) !important;
            border-color: var(--border) !important;
        }

        [data-theme="dark"] .bg-gray-50,
        [data-theme="dark"] .bg-gray-100 {
            background-color: var(--bg-main) !important;
        }

        [data-theme="dark"] .text-gray-900,
        [data-theme="dark"] .text-gray-800,
        [data-theme="dark"] .text-black {
            color: var(--text-main) !important;
        }

        [data-theme="dark"] .text-gray-700,
        [data-theme="dark"] .text-gray-600,
        [data-theme="dark"] .text-gray-500 {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .border-gray-100,
        [data-theme="dark"] .border-gray-200,
        [data-theme="dark"] .border-gray-300 {
            border-color: var(--border) !important;
        }


        [data-theme="dark"] input,
        [data-theme="dark"] select,
        [data-theme="dark"] textarea {
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
            border-color: var(--border) !important;
        }

        /* Apply Variables */
        body {
            font-family: 'Figtree', 'Rajdhani', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
            overflow-x: hidden;
            /* Prevent horizontal scroll */
        }

        /* Responsive Images */
        img,
        video {
            max-width: 100%;
            height: auto;
        }

        .font-tech {
            font-family: 'Orbitron', sans-serif;
        }

        .font-amiri {
            font-family: 'Amiri', serif;
        }

        /* Responsive Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-main);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--text-muted);
            border-radius: 4px;
            opacity: 0.5;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* Sidebar Links & Buttons */
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
            border: 1px solid transparent;
        }

        .sidebar-link:hover {
            background-color: rgba(251, 191, 36, 0.1);
            /* Gold tint */
            color: var(--primary);
            border-color: var(--border);
            box-shadow: var(--shadow);
            transform: translateX(4px);
        }

        /* Light mode specific hover fix */
        :not([data-theme="dark"]) .sidebar-link:hover {
            background-color: #fffbeb;
            /* Amber 50 */
            color: #b45309;
            /* Amber 700 */
            border-color: #fcd34d;
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(251, 191, 36, 0.15) 0%, transparent 100%);
            color: var(--primary);
            border-left: 3px solid var(--accent);
            box-shadow: var(--glow);
        }

        :not([data-theme="dark"]) .sidebar-link.active {
            background: linear-gradient(90deg, #fffbeb 0%, transparent 100%);
            color: #b45309;
            border-left: 3px solid #d97706;
            box-shadow: none;
        }

        /* Header */
        .tech-header {
            background: var(--nav-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        /* Theme Toggle Switch */
        /* Smooth Toggle */
        .theme-toggle-btn {
            background: var(--bg-panel);
            border: 1px solid var(--border);
            color: var(--text-muted);
            box-shadow: var(--shadow);
        }

        .theme-toggle-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
            box-shadow: var(--glow);
        }

        /* Cards & Panels */
        .ui-card {
            background: var(--bg-panel);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            border-radius: 1rem;
            transition: all 0.3s ease;
        }

        .ui-card:hover {
            border-color: var(--primary);
        }
    </style>
</head>

<body class="antialiased" :data-theme="theme">
    <x-ramadan-theme />

    <!-- Header -->
    <nav class="fixed top-0 z-50 w-full tech-header">
        <div class="px-4 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">

                <div class="flex items-center justify-start">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 transition-colors z-50">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <a href="#" class="flex ms-2 md:me-24 items-center gap-2 md:gap-3 group shrink-0">
                        <div
                            class="relative w-8 h-8 md:w-10 md:h-10 rounded-lg overflow-hidden border border-transparent group-hover:border-[var(--primary)] transition-colors duration-300 shadow-md">
                            <img src="{{ asset('assets/gt_logo.jpg') }}"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                alt="GT Logo">
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="self-center font-tech font-bold whitespace-nowrap tracking-widest transition-colors duration-300"
                                style="color: var(--text-main)">
                                <!-- Desktop -->
                                <span class="hidden md:inline text-xl">
                                    GENERATION <span style="color: var(--primary)">TEAM</span>
                                </span>
                                <!-- Mobile -->
                                <span class="md:hidden text-lg">
                                    GT <span style="color: var(--primary)">.</span>
                                </span>
                            </span>
                        </div>
                    </a>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">

                    <!-- Theme Toggle -->
                    <button @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', theme)"
                        class="theme-toggle-btn w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 focus:outline-none">
                        <i class="fas fa-sun text-yellow-500 text-lg" x-show="theme === 'light'"
                            style="display: none;"></i>
                        <i class="fas fa-moon text-cyan-400 text-lg" x-show="theme === 'dark'"
                            style="display: none;"></i>
                    </button>

                    <!-- Wallet (Restricted) -->
                    @if(in_array(auth()->user()->email, ['2420823@batechu.com', '2420324@batechu.com']))
                        <a href="{{ route('wallet.index') }}" class="relative group p-2 mx-1" title="Wallet">
                            <i class="fas fa-wallet text-xl hover:text-blue-500 transition-colors"
                                style="color: var(--text-muted)"></i>
                        </a>
                    @endif

                    <!-- Notifications -->
                    <div class="relative ml-auto" x-data="{ open: false }">
                        <button @click="open = !open" class="relative group p-2">
                            <i class="fas fa-bell text-xl hover:text-[var(--primary)] transition-colors"
                                style="color: var(--text-muted)"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="absolute top-0 right-0 inline-flex items-center justify-center w-2 h-2 rounded-full animate-ping"
                                    style="background-color: var(--primary)"></span>
                                <span
                                    class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold leading-none text-black rounded-full shadow-sm"
                                    style="background-color: var(--primary); box-shadow: var(--glow)">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-4 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden z-50 border border-gray-100 dark:border-gray-700">
                            <div
                                class="p-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500">Notifications</span>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.markAsRead') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-[10px] text-blue-500 font-bold hover:underline">Mark
                                            all read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto custom-scroll">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <a href="{{ $notification->data['action_url'] ?? '#' }}"
                                        class="block p-4 border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <div class="flex gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center flex-shrink-0">
                                                <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold default-text-color">
                                                    {{ $notification->data['title'] ?? 'New Notification' }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                                    {{ $notification->data['message'] ?? '' }}
                                                </p>
                                                <p class="text-[10px] text-gray-400 mt-2">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-8 text-center text-gray-400">
                                        <i class="far fa-bell-slash mb-2 text-lg"></i>
                                        <p class="text-sm">No new notifications</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <div class="flex items-center ms-1 pl-2 md:ms-3 md:pl-4 relative group"
                        style="border-left: 1px solid var(--border)">

                        {{-- Hover Dropdown / Tooltip --}}
                        <div
                            class="absolute top-12 right-0 w-64 bg-gray-900 border border-gray-700 rounded-xl shadow-2xl p-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 transform translate-y-2 group-hover:translate-y-0 text-center">
                            <p class="font-bold text-white text-lg">{{ auth()->user()->name }}</p>
                            <p class="text-cyan-400 text-xs font-mono mb-2 break-all">{{ auth()->user()->email }}</p>
                            <div class="h-px bg-gray-700 my-2"></div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest">
                                {{ auth()->user()->role ?? 'MEMBER' }}
                            </p>
                        </div>

                        <div class="hidden md:block mr-3 text-right cursor-default">
                            <p class="text-sm font-bold font-tech group-hover:text-cyan-400 transition-colors"
                                style="color: var(--text-main)">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-[10px] uppercase tracking-widest font-bold" style="color: var(--primary)">
                                {{ auth()->user()->role ?? 'Member' }}
                            </p>
                        </div>

                        {{-- Profile Icon with Link --}}
                        <a href="{{ route('profile.show') }}"
                            class="flex text-sm rounded-full p-0.5 border shadow-sm transition-all hover:scale-110 hover:shadow-[0_0_15px_var(--primary)] focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                            style="background-color: var(--bg-panel); border-color: var(--primary); box-shadow: var(--glow)">
                            <x-user-avatar :user="auth()->user()" size="w-9 h-9" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full border-r sm:translate-x-0"
        style="background-color: var(--bg-sidebar); border-color: var(--border)" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto flex flex-col justify-between custom-scroll">

            <ul class="space-y-1 font-medium mt-4">
                {{-- External Links --}}
                <li><a href="https://batechu.com/lms/dashboard" class="sidebar-link"><i
                            class="fas fa-home"></i><span>Home</span></a></li>
                <li><a href="https://batechu.com/lms/courses" class="sidebar-link"><i
                            class="fas fa-book"></i><span>Courses</span></a></li>
                <li><a href="https://batechu.com/lms/profile/students" class="sidebar-link"><i
                            class="fas fa-user"></i><span>Profile LMS</span></a></li>

                <li class="pt-4 pb-2">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest opacity-60"
                        style="color: var(--text-muted)">Workspace</p>
                </li>

                <li>
                    <a href="https://batechu.com/lms/results" class="sidebar-link group">
                        <i class="fas fa-chart-line group-hover:text-green-400 transition-colors"></i>
                        <span>Results</span>
                        <span class="ms-auto w-2 h-2 bg-green-500 rounded-full shadow-[0_0_5px_#22c55e]"></span>
                    </a>
                </li>

                <li><a href="https://batechu.com/lms/assignments" class="sidebar-link"><i
                            class="fas fa-clipboard-list"></i><span>Assignments</span></a></li>

                <li><a href="https://batechu.com/lms/timetables" class="sidebar-link"><i
                            class="fas fa-calendar-alt"></i><span>Timetables</span></a></li>

                <li><a href="https://batechu.com/lms/attendance/token" class="sidebar-link"><i
                            class="fas fa-user-check"></i><span>Attendance</span></a></li>
                <li><a href="https://batu-service.vercel.app/token/verify" class="sidebar-link"><i
                            class="fas fa-server"></i><span>Services</span></a></li>

                @if(auth()->check() && in_array(auth()->user()->email, ['2420823@batechu.com', '2420324@batechu.com']))
                    <li>
                        <a href="{{ route('join.admin') }}"
                            class="sidebar-link {{ request()->routeIs('join.admin') ? 'active' : '' }}">
                            <i class="fas fa-user-plus"></i>
                            <span>Join Requests</span>
                            @php $pendingCount = \App\Models\JoinRequest::where('status', 'pending')->count(); @endphp
                            @if($pendingCount > 0)
                                <span
                                    class="inline-flex items-center justify-center w-4 h-4 ms-2 text-[10px] font-semibold text-white bg-red-500 rounded-full">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('projects.index') }}" class="sidebar-link active">
                        <i class="fas fa-project-diagram"></i>
                        <span>Team Projects</span>
                        <span
                            class="inline-flex items-center justify-center px-2 ms-auto text-[10px] font-bold text-white rounded shadow-lg animate-pulse"
                            style="background: linear-gradient(45deg, #ef4444, #f87171);">LIVE</span>
                    </a>
                </li>
            </ul>

            <ul class="pb-4">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="sidebar-link w-full text-red-500 hover:bg-red-500/10 hover:text-red-500 hover:border-l-4 hover:border-red-500 transition-all">
                            <i class="fas fa-power-off"></i>
                            <span>Terminate Session</span>
                        </button>
                    </form>
                </li>
                <li class="mt-4 px-4 text-center">
                    <p class="text-[10px] opacity-40 font-mono" style="color: var(--text-muted)">GENERATION TEAM SYSTEM
                        v2.1</p>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="p-4 sm:ml-64 mt-16 min-h-screen relative overflow-hidden transition-colors duration-300">

        {{-- Background Pattern --}}
        <div class="absolute inset-0 z-0 pointer-events-none opacity-20"
            style="background-image: radial-gradient(var(--grid-color) 1px, transparent 1px); background-size: 20px 20px;">
        </div>

        <div class="relative z-10 transition-all duration-300">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.querySelector('[data-drawer-toggle]').addEventListener('click', function () {
            document.getElementById('logo-sidebar').classList.toggle('-translate-x-full');
        });
    </script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: document.body.getAttribute('data-theme') === 'dark' ? '#1f2937' : '#ffffff',
            color: document.body.getAttribute('data-theme') === 'dark' ? '#fff' : '#1f2937',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Update Toast colors based on dynamic theme check if needed, 
        // but explicit colors work best for alerts.

        @if(session('success'))
            Toast.fire({ icon: 'success', title: '{{ session('success') }}', iconColor: '#00cc66' });
        @endif

        @if(session('error'))
            Toast.fire({ icon: 'error', title: '{{ session('error') }}', iconColor: '#ef4444' });
        @endif

        @if(session('info'))
            Toast.fire({ icon: 'info', title: '{{ session('info') }}', iconColor: '#3b82f6' });
        @endif

        @if($errors->any())
            Toast.fire({ icon: 'warning', title: '{{ $errors->first() }}', iconColor: '#facc15' });
        @endif
    </script>
</body>

</html>