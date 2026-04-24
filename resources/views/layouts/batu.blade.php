<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generation Team - System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        :not(.dark) .sidebar-link:hover {
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

        :not(.dark) .sidebar-link.active {
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

        /* Notification highlight pulse (30s on scroll-to) */
        @keyframes notifHighlightPulse {

            0%,
            100% {
                background-color: transparent;
                box-shadow: none;
            }

            40% {
                background-color: rgba(250, 204, 21, 0.22);
                box-shadow: 0 0 0 4px rgba(250, 204, 21, 0.12);
            }
        }

        .notif-highlight {
            animation: notifHighlightPulse 2s ease-in-out infinite;
            border-radius: 1.25rem;
            transition: background-color 1s ease;
        }
    </style>
    {{-- Auto-Disable Eid Theme on Thursday 26/03/2026 11:59PM --}}
    @if(config('app.eid_theme', false) && \Carbon\Carbon::now()->lt(\Carbon\Carbon::create(2026, 3, 26, 23, 59, 59)))
        @include('partials.eid-theme')
    @endif
</head>

<body class="antialiased">
    {{-- Auto-Disable Eid Theme on Thursday 26/03/2026 11:59PM --}}
    @if(config('app.eid_theme', false) && \Carbon\Carbon::now()->lt(\Carbon\Carbon::create(2026, 3, 26, 23, 59, 59)))
        @include('partials.eid-theme')
    @endif

    <!-- Header -->
    <nav class="fixed top-0 z-50 w-full tech-header">
        <div class="px-4 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">

                <div class="flex items-center justify-start gap-2">
                    @if(!request()->is('/') && !request()->routeIs('dashboard'))
                        <button onclick="window.history.back()" 
                            class="md:hidden flex items-center justify-center w-8 h-8 rounded-full bg-gray-100/50 text-gray-600 active:bg-gray-200 transition-colors">
                            <i class="fas fa-chevron-left text-sm"></i>
                        </button>
                    @endif

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 md:gap-3 group shrink-0">
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
                                <span class="md:hidden text-base">
                                    GENERATION <span style="color: var(--primary)">TEAM</span>
                                </span>
                            </span>
                        </div>
                    </a>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">


                    {{-- Wallet (Always link to index) --}}
                    <a href="{{ route('wallet.index') }}" class="relative group p-2 mx-1 hidden md:block" title="Wallet">
                        <i class="fas fa-wallet text-xl hover:text-blue-500 transition-colors"
                            style="color: var(--text-muted)"></i>
                    </a>

                    {{-- Notifications Bell --}}
                    <div class="flex items-center gap-1 sm:gap-2 relative ml-auto">
                        <!-- Mobile Sign Out Button -->
                        <form method="POST" action="{{ route('logout') }}" class="md:hidden">
                            @csrf
                            <button type="submit" 
                                class="flex items-center justify-center w-9 h-9 rounded-full bg-red-50 text-red-500 active:bg-red-100 transition-colors"
                                title="Sign Out">
                                <i class="fas fa-power-off text-sm"></i>
                            </button>
                        </form>

                        <div class="relative" x-data="{ open: false }">
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
                            class="absolute right-0 mt-4 w-80 bg-white rounded-xl shadow-2xl overflow-hidden z-50 border border-gray-100">
                            {{-- Header --}}
                            <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500">Notifications</span>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <button id="mark-all-read-btn" onclick="markAllNotifRead()"
                                        class="text-[10px] text-blue-500 font-bold hover:underline">
                                        Mark all read
                                    </button>
                                @endif
                            </div>

                            {{-- Notification List --}}
                            <div class="max-h-80 overflow-y-auto custom-scroll" id="notif-list">
                                @forelse(auth()->user()->notifications->take(25) as $notification)
                                    @php
                                        $isRead = !is_null($notification->read_at);
                                        $url = $notification->data['action_url'] ?? ($notification->data['url'] ?? '#');
                                        $title = $notification->data['title'] ?? 'Notification';
                                        $message = $notification->data['message'] ?? ($notification->data['body'] ?? '');
                                        $icon = $notification->data['icon'] ?? 'fas fa-bell';
                                    @endphp
                                    <a href="#" onclick="handleNotifClick(event, '{{ $notification->id }}', '{{ $url }}')"
                                        class="block p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $isRead ? 'opacity-60' : '' }}"
                                        id="notif-{{ $notification->id }}">
                                        <div class="flex gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                                                                                                        {{ $isRead ? 'bg-gray-100 text-gray-400' : 'bg-blue-100 text-blue-500' }}">
                                                <i class="{{ $icon }} text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-1">
                                                    <p class="text-sm font-bold text-gray-800 leading-tight">
                                                        {{ $title }}
                                                    </p>
                                                    @if(!$isRead)
                                                        <span
                                                            class="flex-shrink-0 w-2 h-2 rounded-full bg-blue-500 mt-1"></span>
                                                    @endif
                                                </div>
                                                @if($message)
                                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $message }}</p>
                                                @endif
                                                <p class="text-[10px] text-gray-400 mt-1">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-8 text-center text-gray-400">
                                        <i class="far fa-bell-slash mb-2 text-lg"></i>
                                        <p class="text-sm">No notifications yet</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <div class="hidden md:flex items-center ms-1 pl-2 md:ms-3 md:pl-4 relative group"
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
                            class="flex text-sm rounded-full p-0.5 border shadow-sm transition-all hover:scale-110 hover:shadow-[0_0_15px_var(--primary)] focus:ring-4 focus:ring-gray-300"
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
        class="hidden md:flex fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full border-r md:translate-x-0"
        style="background-color: var(--bg-sidebar); border-color: var(--border)" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto flex flex-col justify-between custom-scroll">

            <ul class="space-y-1 font-medium mt-4">
                {{-- External Links --}}
                <li class="pt-4 pb-2">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest opacity-60"
                        style="color: var(--text-muted)">LMS</p>
                </li>

                <li><a href="https://batechu.com/lms/dashboard" class="sidebar-link"><i
                            class="fas fa-home"></i><span>LMS Home</span></a></li>
                <li><a href="https://batechu.com/lms/courses" class="sidebar-link"><i
                            class="fas fa-book"></i><span>Courses</span></a></li>
                <li><a href="https://batechu.com/lms/profile/students" class="sidebar-link"><i
                            class="fas fa-user"></i><span> LMS Profile</span></a></li>
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


                <li class="pt-4 pb-2">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest opacity-60"
                        style="color: var(--text-muted)">Generation Team</p>
                </li>


                @if(auth()->check() && auth()->user()->canManageJoinRequests())
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
                    @if(auth()->check() && \App\Models\TeamMember::where('user_id', auth()->id())->value('role') === 'leader')
                        <li>
                            <a href="{{ route('posters.index') }}"
                                class="sidebar-link {{ request()->routeIs('posters.*') ? 'active' : '' }}">
                                <i class="fas fa-images text-yellow-500"></i>
                                <span>Posters</span>
                                <span
                                    class="inline-flex items-center justify-center px-1.5 py-0.5 ms-2 text-[10px] font-bold text-yellow-800 bg-yellow-100 rounded border border-yellow-200">CMS</span>
                            </a>
                        </li>
                    @endif

                {{-- Quiz System Links --}}
                <li>
                    <a href="{{ route('quizzes.index') }}"
                        class="sidebar-link {{ request()->routeIs('quizzes.*') ? 'active' : '' }}">
                        <i class="fas fa-question-circle text-orange-500"></i>
                        <span>Quizzes</span>
                    </a>
                </li>
                @if(auth()->check() && auth()->user()->hasPermission('manage_quizzes'))
                    <li>
                        <a href="{{ route('admin.quizzes.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
                            <i class="fas fa-cogs text-orange-700"></i>
                            <span>Manage Quizzes</span>
                            <span
                                class="inline-flex items-center justify-center px-1.5 py-0.5 ms-2 text-[10px] font-bold text-orange-800 bg-orange-100 rounded border border-orange-200">Admin</span>
                        </a>
                    </li>
                @endif


                @php
                    $member = \App\Models\TeamMember::where('user_id', auth()->id())->first();
                    $myRole = $member ? $member->role : null;
                    $isSubLeader = $member ? $member->is_sub_leader : false;
                    $myTeamId = $member ? $member->team_id : null;
                @endphp

                @if($myTeamId && (in_array($myRole, ['leader', 'vice_leader']) || $isSubLeader))
                    <li>
                        <a href="{{ route('evaluation.index', $myTeamId) }}"
                            class="sidebar-link {{ request()->routeIs('evaluation.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-check text-indigo-500"></i>
                            <span> Weekly Evaluation</span>
                        </a>
                    </li>
                @endif
            </ul>

            <ul class="pb-4">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="sidebar-link w-full text-red-500 hover:bg-red-500/10 hover:text-red-500 hover:border-l-4 hover:border-red-500 transition-all">
                            <i class="fas fa-power-off"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </li>
                <li class="mt-4 px-4 text-center">
                    <p class="text-[10px] opacity-40 font-mono" style="color: var(--text-muted)">GENERATION TEAM SYSTEM
                        v2.5</p>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="p-4 md:ml-64 mt-16 pb-20 md:pb-4 min-h-screen relative overflow-hidden transition-colors duration-300">

        {{-- Background Pattern --}}
        <div class="absolute inset-0 z-0 pointer-events-none opacity-20"
            style="background-image: radial-gradient(var(--grid-color) 1px, transparent 1px); background-size: 20px 20px;">
        </div>

        <div id="main-content-wrapper" class="relative z-10 transition-all duration-300">
            @yield('content')
        </div>
    </div>

    <!-- Mobile Bottom Navigation (Facebook Style) -->
    <nav id="mobile-bottom-nav" class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 z-50 shadow-[0_-8px_20px_rgba(0,0,0,0.08)] flex items-center justify-between px-1 pb-safe transition-transform duration-300 transform" style="height: 70px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(15px);">
        
        @php
            $user = auth()->user();
            $member = \App\Models\TeamMember::where('user_id', $user->id)->first();
            $myRole = $member ? $member->role : null;
            $isSubLeader = $member ? ($member->is_sub_leader ?? false) : false;
            $myTeamId = $member ? $member->team_id : null;
            
            // Permissions
            $isLeader = $myRole === 'leader';
            $canSeePosters = $isLeader;
            $canManageQuizzes = $user->hasPermission('manage_quizzes');
            $canSeeEval = $myTeamId && (in_array($myRole, ['leader', 'vice_leader']) || $isSubLeader);
            $canManageRequests = $user->canManageJoinRequests();

            // Notification Counts
            $walletNotifs = 0;
            if ($isLeader) {
                $walletNotifs = \App\Models\WalletDepositRequest::where('status', 'pending')->count();
            } else {
                $walletNotifs = \App\Models\FundContribution::where('user_id', $user->id)->where('status', 'pending')->count();
            }

            $quizNotifs = 0;
            if ($canManageQuizzes) {
                $quizNotifs = \App\Models\QuizRetryRequest::where('status', 'pending')->count();
            } else {
                 $quizNotifs = \App\Models\Quiz::whereDoesntHave('attempts', function($q) use ($user) {
                     $q->where('user_id', $user->id);
                 })->where('is_published', true)->count();
            }

            $requestNotifs = $canManageRequests ? \App\Models\JoinRequest::where('status', 'pending')->count() : 0;
            
            $projectNotifs = 0;
            if ($isLeader) {
                 $projectNotifs = \App\Models\TaskSubmission::where('status', 'pending')->count();
            } else {
                $projectNotifs = \App\Models\Task::whereDoesntHave('submissions', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count();
            }

            $evalNotifs = 0; 
            if ($canSeeEval) {
            }

            $moreNotifs = $requestNotifs + $evalNotifs;
        @endphp

        <!-- 1) LMS Popup Button -->
        <div class="flex-none w-[16%]" x-data="{ openLMS: false }">
            <button @click="openLMS = !openLMS" @click.away="openLMS = false" class="w-full h-full flex flex-col items-center justify-center gap-1 transition-all">
                <div class="w-9 h-9 flex items-center justify-center rounded-xl transition-colors" :class="openLMS ? 'bg-indigo-50 text-[var(--primary)]' : 'text-gray-400'">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-tight" :class="openLMS ? 'text-[var(--primary)]' : 'text-gray-500'">LMS</span>
            </button>
            
            <!-- LMS Popup Menu -->
            <div x-show="openLMS" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="absolute bottom-[100%] left-2 mb-3 w-56 bg-white rounded-2xl shadow-[0_15px_50px_rgba(0,0,0,0.2)] border border-gray-100 overflow-hidden transform origin-bottom-left z-50">
                <div class="p-3 border-b border-gray-100 bg-gray-50 uppercase text-[10px] font-black text-gray-400 text-center tracking-widest">System Portal</div>
                <div class="py-2 max-h-[60vh] overflow-y-auto custom-scroll">
                    <a href="https://batechu.com/lms/dashboard" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50"><i class="fas fa-home w-8 text-indigo-500"></i> Home</a>
                    <a href="https://batechu.com/lms/courses" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50"><i class="fas fa-book w-8 text-blue-500"></i> Courses</a>
                    <a href="https://batechu.com/lms/results" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-chart-line w-8 text-green-500"></i> 
                        <span>Results</span>
                    </a>
                    <a href="https://batechu.com/lms/assignments" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50"><i class="fas fa-tasks w-8 text-orange-500"></i> Assignments</a>
                    <a href="https://batechu.com/lms/timetables" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50"><i class="fas fa-calendar-alt w-8 text-purple-500"></i> Timetables</a>
                    <a href="https://batechu.com/lms/attendance/token" class="flex items-center px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50"><i class="fas fa-user-check w-8 text-cyan-500"></i> Attendance</a>
                    <div class="h-px bg-gray-100 my-2"></div>
                    <form method="POST" action="{{ route('logout') }}" class="px-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-3 text-sm font-black text-red-500 hover:bg-red-50"><i class="fas fa-power-off w-8"></i> SIGN OUT</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Dynamic Generation Items (Main) -->
        <div class="flex-1 flex items-center justify-around space-x-0 overflow-hidden">
            <a href="{{ route('projects.index') }}" class="w-[75px] flex flex-col items-center justify-center gap-1 transition-all {{ request()->routeIs('projects.*') ? 'text-indigo-600 font-black' : 'text-gray-400' }}">
                <div class="w-10 h-10 flex items-center justify-center rounded-full relative {{ request()->routeIs('projects.*') ? 'bg-indigo-50 border border-indigo-100 shadow-sm' : '' }}">
                    <i class="fas fa-project-diagram text-xl"></i>
                    @if($projectNotifs > 0)
                        <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-[10px] font-black text-white bg-red-500 rounded-full border-2 border-white">{{ $projectNotifs }}</span>
                    @endif
                </div>
                <span class="text-[11px] font-black uppercase tracking-tighter">Projects</span>
            </a>

            <a href="{{ route('quizzes.index') }}" class="w-[75px] flex flex-col items-center justify-center gap-1 transition-all {{ request()->routeIs('quizzes.*') ? 'text-orange-500 font-black' : 'text-gray-400' }}">
                <div class="w-10 h-10 flex items-center justify-center rounded-full relative {{ request()->routeIs('quizzes.*') ? 'bg-orange-50 border border-orange-100 shadow-sm' : '' }}">
                    <i class="fas fa-question-circle text-xl"></i>
                    @if($quizNotifs > 0)
                        <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-[10px] font-black text-white bg-red-500 rounded-full border-2 border-white">{{ $quizNotifs }}</span>
                    @endif
                </div>
                <span class="text-[11px] font-black uppercase tracking-tighter">Quizzes</span>
            </a>
        </div>

        <!-- 2) More Menu (Overflow Items) -->
        <div class="flex-none w-[17%]" x-data="{ openMore: false }">
            <button @click="openMore = !openMore" @click.away="openMore = false" class="w-full h-full flex flex-col items-center justify-center gap-1 transition-all">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl transition-colors relative" :class="openMore ? 'bg-indigo-50 text-indigo-600 border border-indigo-100 shadow-sm' : 'text-gray-400'">
                    <i class="fas fa-ellipsis-h text-xl"></i>
                    @if($moreNotifs > 0)
                        <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-[10px] font-black text-white bg-red-500 rounded-full border-2 border-white">{{ $moreNotifs }}</span>
                    @endif
                </div>
                <span class="text-[11px] font-black uppercase tracking-tighter" :class="openMore ? 'text-indigo-600' : 'text-gray-500'">More</span>
            </button>

            <div x-show="openMore" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="absolute bottom-[100%] right-10 mb-3 w-60 bg-white rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.25)] border border-gray-100 overflow-hidden transform origin-bottom-right z-50">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50 uppercase text-[11px] font-black text-gray-400 text-center tracking-[0.15em]">Generation Tools</div>
                <div class="py-2 max-h-[60vh] overflow-y-auto custom-scroll">
                    @if($canManageRequests)
                        <a href="{{ route('join.admin') }}" class="flex items-center px-5 py-3.5 text-sm font-bold text-gray-700 hover:bg-indigo-50 transition-colors {{ request()->routeIs('join.admin') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                            <i class="fas fa-user-plus w-10 text-indigo-500 text-lg"></i>
                            <span class="flex-1">Join Requests</span>
                            @if($requestNotifs > 0)
                                <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black">{{ $requestNotifs }}</span>
                            @endif
                        </a>
                    @endif

                    @if($canSeePosters)
                        <a href="{{ route('posters.index') }}" class="flex items-center px-5 py-3.5 text-sm font-bold text-gray-700 hover:bg-yellow-50 transition-colors {{ request()->routeIs('posters.*') ? 'text-yellow-600 bg-yellow-50' : '' }}">
                            <i class="fas fa-images w-10 text-yellow-500 text-lg"></i>
                            <span class="flex-1">Posters</span>
                        </a>
                    @endif
                    
                    @if($canManageQuizzes)
                        <a href="{{ route('admin.quizzes.index') }}" class="flex items-center px-5 py-3.5 text-sm font-bold text-gray-700 hover:bg-orange-50 transition-colors {{ request()->routeIs('admin.quizzes.*') ? 'text-orange-700 bg-orange-50' : '' }}">
                            <i class="fas fa-cogs w-10 text-orange-700 text-lg"></i>
                            <span class="flex-1">Manage Quizzes</span>
                        </a>
                    @endif

                    @if($canSeeEval)
                        <a href="{{ route('evaluation.index', $myTeamId) }}" class="flex items-center px-5 py-3.5 text-sm font-bold text-gray-700 hover:bg-indigo-50 transition-colors {{ request()->routeIs('evaluation.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                            <i class="fas fa-clipboard-check w-10 text-indigo-500 text-lg"></i>
                            <span class="flex-1">Weekly Eval</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3) Wallet & Profile (Anchored) -->
        <div class="flex-none flex items-center justify-end px-1 gap-0">
            <a href="{{ route('wallet.index') }}" class="w-[60px] flex flex-col items-center justify-center gap-1 {{ request()->routeIs('wallet.*') ? 'text-indigo-600 font-black' : 'text-gray-400' }}">
                <div class="w-10 h-10 flex items-center justify-center rounded-full relative {{ request()->routeIs('wallet.*') ? 'bg-indigo-50' : '' }}">
                    <i class="fas fa-wallet text-xl"></i>
                    @if($walletNotifs > 0)
                        <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-[10px] font-black text-white bg-red-500 rounded-full border-2 border-white">{{ $walletNotifs }}</span>
                    @endif
                </div>
                <span class="text-[10px] font-black uppercase tracking-tight">Wallet</span>
            </a>

            <a href="{{ route('profile.show') }}" class="w-[60px] flex flex-col items-center justify-center gap-1">
                <div class="w-10 h-10 flex items-center justify-center rounded-full overflow-hidden border-2 p-0.5 {{ request()->routeIs('profile.*') ? 'border-indigo-600 scale-105' : 'border-gray-200' }}">
                    <x-user-avatar :user="auth()->user()" size="w-full h-full" />
                </div>
                <span class="text-[10px] font-black uppercase tracking-tight {{ request()->routeIs('profile.*') ? 'text-indigo-600' : 'text-gray-500' }}">Profile</span>
            </a>
        </div>
    </nav>

    <script>
        // Global Mobile Navigation Scroll Behavior (Facebook Style)
        (function() {
            let lastScrollY = window.scrollY;
            const bottomNav = document.getElementById('mobile-bottom-nav');
            if (!bottomNav) return;

            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;
                const scrollDelta = currentScrollY - lastScrollY;

                // Only trigger if scrolled more than a small threshold to prevent jitter
                if (Math.abs(scrollDelta) > 5) {
                    if (currentScrollY > lastScrollY && currentScrollY > 100) {
                        // Scrolling Down - Hide Bar
                        bottomNav.style.transform = 'translateY(100%)';
                    } else {
                        // Scrolling Up - Show Bar
                        bottomNav.style.transform = 'translateY(0)';
                    }
                }
                lastScrollY = currentScrollY;
            }, { passive: true });
        })();
    </script>

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
            background: '#ffffff',
            color: '#1f2937',
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

    {{-- ============================================================== --}}
    {{-- 🔔 Notification JS: mark-all (AJAX), click-redirect, highlight --}}
    {{-- ============================================================== --}}
    <script>
            /**
             * Global SweetAlert Confirmation for Forms
             * Use: onsubmit="return confirmFormSubmit(event, this, 'Are you sure?')"
             */
            function confirmFormSubmit(event, form, message) {
                event.preventDefault();
                Swal.fire({
                    title: 'Confirmation Required',
                    text: message || 'Are you sure you want to perform this action?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#e5e7eb',
                    confirmButtonText: 'Yes, Proceed',
                    cancelButtonText: 'Cancel',
                    background: '#ffffff',
                    color: '#111827',
                    customClass: {
                        cancelButton: 'text-gray-900',
                        confirmButton: 'text-white font-bold',
                        popup: 'rounded-3xl border border-gray-100 shadow-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Trigger AJAX submission
                        handleAjaxFormSubmit({ preventDefault: () => { }, target: form });
                    }
                });
                return false;
            }

        /**
         * Global SweetAlert Confirmation for Links/Buttons
         * Use: onclick="return confirmAction(event, 'Message', 'url')"
         */
        function confirmAction(event, message, urlOrCallback) {
            event.preventDefault();
            Swal.fire({
                title: 'Action Required',
                text: message || 'Are you sure?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#e5e7eb',
                confirmButtonText: 'Confirm',
                background: '#ffffff',
                color: '#111827',
                customClass: {
                    cancelButton: 'text-gray-900',
                    popup: 'rounded-3xl border border-gray-100 shadow-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof urlOrCallback === 'function') {
                        // If it's a function that tries to submit the form, intercept it
                        const form = event.target.closest('form');
                        if (form) {
                            handleAjaxFormSubmit({ preventDefault: () => { }, target: form });
                        } else {
                            urlOrCallback();
                        }
                    } else {
                        window.location.href = urlOrCallback;
                    }
                }
            });
            return false;
        }

        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content
            || '{{ csrf_token() }}';

        /**
         * Mark ALL unread notifications via AJAX (fixes the print-page bug).
         */
        function markAllNotifRead() {
            fetch('{{ route("notifications.markAsRead") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Dim all notification rows and remove unread dots
                        document.querySelectorAll('#notif-list a').forEach(el => el.classList.add('opacity-60'));
                        document.querySelectorAll('#notif-list .bg-blue-500.rounded-full').forEach(dot => dot.remove());
                        document.querySelectorAll('#notif-list .bg-blue-100').forEach(ic => {
                            ic.classList.replace('bg-blue-100', 'bg-gray-100');
                            ic.classList.replace('text-blue-500', 'text-gray-400');
                        });

                        // Hide the mark-all button
                        const btn = document.getElementById('mark-all-read-btn');
                        if (btn) btn.remove();
                    }
                })
                .catch(console.error);
        }

        /**
         * Handle single notification click:
         *  1. Mark that specific notification as read (AJAX)
         *  2. Navigate to action_url (which may include a #hash)
         */
        function handleNotifClick(event, notifId, url) {
            event.preventDefault();

            fetch('{{ route("notifications.markAsRead") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ notification_id: notifId })
            })
                .then(r => r.json())
                .then(() => {
                    // Navigate to the target URL (may contain #anchor)
                    if (url && url !== '#') {
                        window.location.href = url;
                    }
                })
                .catch(() => {
                    // Fallback: navigate anyway
                    if (url && url !== '#') window.location.href = url;
                });
        }

        /**
         * On page load: check URL hash → scroll to element → apply 30-second highlight.
         */
        /**
         * GLOBAL AJAX HANDLER
         * Intercepts form submissions and performs them via fetch()
         */
        /**
         * GLOBAL SEAMLESS AJAX HANDLER
         * Intercepts form submissions and updates the UI without a full page refresh.
         */
        async function handleAjaxFormSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = event.submitter || form.querySelector('[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            const formData = new FormData(form);

            if (event.submitter && event.submitter.name) {
                formData.append(event.submitter.name, event.submitter.value);
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.minWidth = `${submitBtn.offsetWidth}px`;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
            showTopLoading();

            try {
                const url = form.getAttribute('action') || window.location.href;
                const method = form.getAttribute('method') || 'POST';
                let fetchUrl = url;
                let fetchOptions = {
                    method: method.toUpperCase(),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                };

                if (method.toUpperCase() === 'GET') {
                    const params = new URLSearchParams(new FormData(form)).toString();
                    if (params) {
                        fetchUrl += (fetchUrl.includes('?') ? '&' : '?') + params;
                    }
                } else {
                    fetchOptions.body = formData;
                }

                const response = await fetch(fetchUrl, fetchOptions);
                const result = await response.json();

                if (result.success) {
                    if (result.message) Toast.fire({ icon: 'success', title: result.message });

                    // Close Modals
                    if (typeof closeModal === 'function') {
                        const modalId = form.closest('[role="dialog"]')?.id || form.closest('.royal-modal-active')?.id;
                        if (modalId) closeModal(modalId);
                    } else {
                        const modal = form.closest('.royal-modal-active') || form.closest('[x-data]');
                        if (modal && modal.__x) { modal.__x.$data.open = false; }
                        else if (modal) { modal.classList.add('hidden'); modal.style.display = 'none'; }
                    }

                    // Perform Seamless DOM Swap
                    const targetUrl = result.redirect || fetchUrl;
                    await refreshMainContent(targetUrl);
                    window.history.pushState({}, '', targetUrl);

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: result.message || 'Something went wrong!',
                        background: '#ffffff',
                        color: '#111827',
                    });
                }
            } catch (error) {
                console.error('AJAX Error:', error);
                Toast.fire({ icon: 'error', title: 'Network error or session expired.' });
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
                hideTopLoading();
            }
        }

        /**
         * Re-fetches the current page and swaps the main content area.
         */
        async function refreshMainContent(url) {
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('main-content-wrapper');
                const currentContent = document.getElementById('main-content-wrapper');

                if (newContent && currentContent) {
                    // Update content
                    currentContent.innerHTML = newContent.innerHTML;

                    // Re-initialize scripts/plugins if necessary
                    if (typeof applyNotifHighlight === 'function') applyNotifHighlight();

                    // Re-trigger Alpine.js if present
                    if (window.Alpine) {
                        window.Alpine.discover();
                    }

                    // Dispatch a global event so other components can react
                    window.dispatchEvent(new CustomEvent('gt-content-updated'));
                }
            } catch (error) {
                console.error('Swap Error:', error);
                window.location.reload(); // Last resort fallback
            }
        }

        /**
         * Top Loading Bar Controls
         */
        function showTopLoading() {
            let bar = document.getElementById('top-loading-bar');
            if (!bar) {
                bar = document.createElement('div');
                bar.id = 'top-loading-bar';
                bar.style = 'position: fixed; top: 0; left: 0; height: 3px; background: #fbbf24; z-index: 9999; transition: width 0.3s ease; width: 0; box-shadow: 0 0 10px #fbbf24;';
                document.body.appendChild(bar);
            }
            bar.style.width = '30%';
            setTimeout(() => { if (bar.style.width === '30%') bar.style.width = '70%'; }, 500);
        }

        function hideTopLoading() {
            const bar = document.getElementById('top-loading-bar');
            if (bar) {
                bar.style.width = '100%';
                setTimeout(() => { bar.style.opacity = '0'; setTimeout(() => bar.remove(), 300); }, 500);
            }
        }

        /**
         * Global AJAX confirm wrapper
         */
        function handleAjaxAction(event, form, message) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: message || 'You are about to perform this action.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, proceed',
                background: '#ffffff',
                color: '#111827',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Use the AJAX submit handler
                    const fakeEvent = { preventDefault: () => { }, target: form };
                    handleAjaxFormSubmit(fakeEvent);
                }
            });
            return false;
        }

        (function applyNotifHighlight() {
            const hash = window.location.hash; // e.g. "#budget-section"
            if (!hash) return;

            // Small delay so page content has rendered
            setTimeout(function () {
                const el = document.querySelector(hash);
                if (!el) return;

                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                el.classList.add('notif-highlight');

                // Remove highlight after 30 seconds
                setTimeout(function () {
                    el.classList.remove('notif-highlight');
                }, 30000);
            }, 400);
        })();
    </script>
</body>

</html>