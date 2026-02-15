@extends('layouts.staff')

@section('content')

    {{--
    ██████████████████████████████████████████████████████████████████████████
    💎 PREMIUM DASHBOARD CONFIGURATION & STYLES
    ██████████████████████████████████████████████████████████████████████████
    --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Outfit:wght@300;400;500;700;900&display=swap');

        /* --- Global Reset & Base --- */
        [x-cloak] {
            display: none !important;
        }

        :root {
            --primary-dark: #0f172a;
            --primary-teal: #175c53;
            --accent-gold: #D4AF37;
            --accent-gold-light: #fcf6ba;
            --danger-red: #ef4444;
            --success-green: #10b981;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Outfit', 'Cairo', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* --- Advanced Backgrounds --- */
        .premium-dashboard-bg {
            background: #f8fafc;
            background-image:
                radial-gradient(circle at 0% 0%, rgba(23, 92, 83, 0.08) 0px, transparent 50%),
                radial-gradient(circle at 100% 0%, rgba(212, 175, 55, 0.05) 0px, transparent 40%),
                radial-gradient(circle at 100% 100%, rgba(15, 23, 42, 0.05) 0px, transparent 50%);
            min-height: 100vh;
            position: relative;
        }

        /* --- Glassmorphism Utilities --- */
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.02),
                0 10px 15px -3px rgba(0, 0, 0, 0.03),
                inset 0 0 20px rgba(255, 255, 255, 0.5);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            transform: translateY(-5px) scale(1.005);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
            border-color: #fff;
            background: rgba(255, 255, 255, 0.9);
        }

        /* --- Scroll Progress --- */
        #scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, #bf953f, #fcf6ba, #175c53);
            z-index: 9999;
            width: 0%;
            transition: width 0.1s ease-out;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
        }

        /* --- Custom Animations --- */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shine {
            0% {
                background-position: 200% center;
            }

            100% {
                background-position: -200% center;
            }
        }

        .animate-premium {
            animation: fadeSlideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        /* --- Scrollbar --- */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--primary-teal);
        }

        /* --- Sticky Nav Micro-Interaction --- */
        .sticky-nav-active {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(20px);
            padding: 8px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: translateY(5px);
        }
    </style>

    {{-- Global State Management (Alpine) --}}
    <div x-data="{
        showScrollTop: false,
        loading: false,
        activeSection: '',
        init() {
            window.addEventListener('scroll', () => {
                this.showScrollTop = window.pageYOffset > 500;
            });
        }
    }">

        {{-- Top Loading Bar --}}
        {{-- <div id="scroll-progress"></div> --}}

        {{-- Loading Overlay (When submitting forms) --}}
        {{-- <div x-show="loading" x-transition.opacity
            class="fixed inset-0 z-[9999] bg-slate-900/80 backdrop-blur-sm flex items-center justify-center">
            <div class="flex flex-col items-center">
                <i class="fas fa-circle-notch fa-spin text-5xl text-[#D4AF37] mb-4"></i>
                <p class="text-white font-bold tracking-widest uppercase text-xs">Processing Request...</p>
            </div> --}}
        {{--
        </div> --}}

        <div class="premium-dashboard-bg p-4 md:p-8 lg:p-12 space-y-12">

            {{--
            ██████████████████████████████████████████████████████████████████████████
            👑 SECTION 1: HERO HEADER (واجهة المشروع)
            ██████████████████████████████████████████████████████████████████████████
            --}}
            <div id="hero-section"
                class="relative rounded-[3rem] overflow-hidden shadow-2xl animate-premium group border border-white/20 select-none">

                {{-- Dynamic Backgrounds --}}
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-[#175c53] to-slate-950 opacity-95"></div>
                <div
                    class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay">
                </div>

                {{-- Ambient Light Effects --}}
                <div
                    class="absolute -top-32 -right-32 w-96 h-96 bg-[#D4AF37] opacity-10 rounded-full blur-[120px] group-hover:opacity-20 transition-opacity duration-1000">
                </div>
                <div class="absolute bottom-0 left-0 w-80 h-80 bg-emerald-500 opacity-10 rounded-full blur-[100px]"></div>

                <div class="relative z-10 p-8 md:p-16 text-white">
                    <div class="flex flex-col lg:flex-row justify-between items-center gap-12">

                        {{-- Left Side: Project Info --}}
                        <div class="flex-1 space-y-8 text-center lg:text-left">
                            <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4">
                                <span
                                    class="px-5 py-2 rounded-full bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-[0.3em] text-[#D4AF37] backdrop-blur-md shadow-lg ring-1 ring-white/5">
                                    {{ $team->project_phase ?? 'Strategic Phase' }}
                                </span>
                                <div
                                    class="hidden md:block h-px w-16 bg-gradient-to-r from-[#D4AF37] to-transparent opacity-50">
                                </div>
                                <span
                                    class="text-xs font-bold text-gray-400 font-mono tracking-widest bg-black/20 px-3 py-1 rounded-lg">
                                    ID: GP-{{ str_pad($team->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>

                            <h1
                                class="text-4xl md:text-5xl lg:text-7xl font-black leading-tight tracking-tight drop-shadow-2xl bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-100 to-gray-400">
                                {{ $team->proposal_title }}
                            </h1>

                            <p
                                class="text-lg md:text-xl text-gray-300 max-w-3xl leading-relaxed font-light border-l-4 border-[#D4AF37]/60 pl-6 py-2 bg-gradient-to-r from-white/5 to-transparent rounded-r-xl">
                                "{{ $team->proposal_description }}"
                            </p>

                            <div
                                class="flex flex-wrap items-center justify-center lg:justify-start gap-6 pt-6 border-t border-white/5">
                                {{-- Leader Badge --}}
                                <div
                                    class="flex items-center gap-4 bg-white/5 p-3 pr-6 rounded-2xl border border-white/10 hover:bg-white/10 transition-all cursor-default backdrop-blur-sm">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-gradient-to-tr from-[#D4AF37] to-[#fcf6ba] flex items-center justify-center text-slate-900 shadow-lg shadow-yellow-900/20 transform hover:rotate-12 transition-transform duration-500">
                                        <i class="fas fa-crown text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-wider">Project
                                            Leader</p>
                                        <p class="text-sm font-bold text-white">{{ $team->leader->name }}</p>
                                    </div>
                                </div>

                                {{-- Team Count Badge --}}
                                <button onclick="document.getElementById('membersModal').classList.remove('hidden')"
                                    class="flex items-center gap-4 bg-white/5 p-3 pr-6 rounded-2xl border border-white/10 hover:bg-white/15 hover:border-blue-400/30 transition-all group/btn">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-xl shadow-inner group-hover/btn:bg-blue-600 group-hover/btn:text-white transition-colors duration-300">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-wider">Task Force
                                        </p>
                                        <p
                                            class="text-sm font-bold text-white group-hover/btn:text-blue-300 transition-colors">
                                            {{ $team->members->count() }} Members <i
                                                class="fas fa-arrow-right text-[10px] ml-1 opacity-0 group-hover/btn:opacity-100 transform translate-x-2 group-hover/btn:translate-x-0 transition-all"></i>
                                        </p>
                                    </div>
                                </button>

                                <div class="hidden lg:block h-10 w-px bg-white/10 mx-2"></div>

                                {{-- Team Name --}}
                                <div
                                    class="flex items-center gap-3 bg-[#D4AF37]/10 px-6 py-3 rounded-2xl border border-[#D4AF37]/20">
                                    <i class="fas fa-id-card text-[#D4AF37]"></i>
                                    <span
                                        class="font-black text-xl text-white tracking-widest uppercase">{{ $team->name }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Right Side: Action Buttons --}}
                        <div class="flex flex-col gap-5 w-full lg:w-80">
                            <a href="{{ route('staff.team.view_as', $team->id) }}" target="_blank"
                                class="group relative overflow-hidden bg-white text-slate-900 px-8 py-6 rounded-2xl font-black shadow-[0_20px_40px_rgba(0,0,0,0.2)] hover:shadow-white/20 transition-all transform hover:-translate-y-1 text-center uppercase tracking-tighter border border-white">
                                <span class="relative z-10 flex items-center justify-center gap-3">
                                    <i class="fas fa-rocket text-xl text-emerald-600 group-hover:animate-pulse"></i> Live
                                    Simulation
                                </span>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-gray-100 to-white transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-500 ease-out">
                                </div>
                            </a>

                            <a href="{{ route('staff.my_teams') }}"
                                class="px-8 py-5 rounded-2xl bg-black/40 text-gray-300 hover:text-white hover:bg-black/60 border border-white/10 text-center text-xs font-bold transition-all flex items-center justify-center gap-3 backdrop-blur-md hover:border-white/30 group">
                                <div
                                    class="w-6 h-6 rounded-full border border-white/20 flex items-center justify-center group-hover:border-white transition-colors">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </div>
                                Return to Command Center
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{--
            ⚓ STICKY QUICK NAVIGATION BAR
            --}}
            <div class="sticky top-6 z-40 hidden md:flex justify-center animate-premium delay-100 pointer-events-none">
                <div
                    class="bg-white/80 backdrop-blur-xl border border-white/60 shadow-xl shadow-slate-200/50 rounded-full px-2 py-2 pointer-events-auto flex items-center gap-1 transition-all duration-300 hover:bg-white">
                    <a href="#issues-section"
                        class="px-5 py-2.5 rounded-full text-[10px] font-black uppercase hover:bg-slate-900 hover:text-white transition-all text-slate-600 hover:shadow-lg">Issues</a>
                    <a href="#reports-section"
                        class="px-5 py-2.5 rounded-full text-[10px] font-black uppercase hover:bg-slate-900 hover:text-white transition-all text-slate-600 hover:shadow-lg">Reports</a>
                    <a href="#defense-section"
                        class="px-5 py-2.5 rounded-full text-[10px] font-black uppercase hover:bg-[#D4AF37] hover:text-white transition-all text-slate-600 hover:shadow-lg">Defense</a>
                    <a href="#meetings-section"
                        class="px-5 py-2.5 rounded-full text-[10px] font-black uppercase hover:bg-slate-900 hover:text-white transition-all text-slate-600 hover:shadow-lg">Meetings</a>
                    <a href="#artifacts-section"
                        class="px-5 py-2.5 rounded-full text-[10px] font-black uppercase hover:bg-slate-900 hover:text-white transition-all text-slate-600 hover:shadow-lg">Vault</a>
                </div>
            </div>

            {{--
            ██████████████████████████████████████████████████████████████████████████
            📊 SECTION 2: MAIN GRID (Issues, Reports, Defense)
            ██████████████████████████████████████████████████████████████████████████
            --}}
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 lg:gap-10 animate-premium delay-200">

                {{-- 🟢 LEFT COLUMN: ISSUES & REPORTS (8 Columns) --}}
                <div class="xl:col-span-8 space-y-10">

                    {{-- A. Complaints Section --}}
                    <div id="issues-section" class="glass-card rounded-[2.5rem] overflow-hidden border-t-4 border-red-500">
                        <div
                            class="px-8 py-6 flex flex-wrap justify-between items-center border-b border-gray-100 bg-red-50/40">
                            <div class="flex items-center gap-5">
                                <div
                                    class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-red-600 shadow-md border border-red-100 animate-pulse">
                                    <i class="fas fa-shield-exclamation text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-gray-800 text-xl tracking-tight">Team Issues & Conflicts</h3>
                                    <p class="text-xs text-gray-500 font-medium uppercase mt-1 tracking-wider">Internal Team
                                        Feedback</p>
                                </div>
                            </div>
                            <span
                                class="px-5 py-2 bg-red-600 text-white text-[10px] font-black rounded-xl shadow-lg shadow-red-200 uppercase tracking-widest mt-2 sm:mt-0">
                                {{ $team->memberReports->count() }} Active Issues
                            </span>
                        </div>

                        <div class="overflow-x-auto custom-scrollbar max-h-[450px]">
                            <table class="w-full text-left border-collapse">
                                <thead
                                    class="sticky top-0 bg-gray-50/95 backdrop-blur-sm text-gray-400 text-[10px] uppercase font-black tracking-widest z-10 border-b border-gray-200">
                                    <tr>
                                        <th class="px-8 py-5">Originator</th>
                                        <th class="px-8 py-5">Target Member</th>
                                        <th class="px-8 py-5">Issue Insight</th>
                                        <th class="px-8 py-5">Logged At</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 bg-white">
                                    @forelse($team->memberReports as $report)
                                        <tr class="hover:bg-red-50/30 transition-all duration-300 group">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600 border border-slate-200 group-hover:bg-slate-800 group-hover:text-white transition-colors">
                                                        {{ substr(\App\Models\User::find($report->reporter_id)->name, 0, 1) }}
                                                    </div>
                                                    <span
                                                        class="font-bold text-gray-700 text-sm">{{ \App\Models\User::find($report->reporter_id)->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-black border border-red-100 shadow-sm group-hover:bg-red-100 transition-colors">
                                                    <i class="fas fa-user-minus mr-2 text-[10px]"></i>
                                                    {{ \App\Models\User::find($report->reported_user_id)->name }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6">
                                                <p
                                                    class="text-sm text-gray-600 italic leading-relaxed group-hover:text-red-900 transition-colors">
                                                    "{{ Str::limit($report->complaint, 80) }}"
                                                </p>
                                            </td>
                                            <td class="px-8 py-6 text-[11px] font-mono text-gray-400">
                                                {{ $report->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-24 text-center">
                                                <div class="flex flex-col items-center justify-center space-y-4 opacity-50">
                                                    <div
                                                        class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-2">
                                                        <i class="fas fa-dove text-4xl text-emerald-500"></i>
                                                    </div>
                                                    <p class="text-lg font-bold text-gray-600 uppercase tracking-widest">
                                                        Harmonious Environment</p>
                                                    <p class="text-xs text-gray-400">No conflicts reported at this time.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- B. Weekly Reports Table --}}
                    <div id="reports-section"
                        class="glass-card rounded-[2.5rem] shadow-sm overflow-hidden group hover:shadow-xl transition-all duration-500 relative bg-white">
                        {{-- Top Decorative Line --}}
                        <div
                            class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-500">
                        </div>

                        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                                <span
                                    class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-sm"><i
                                        class="fas fa-clipboard-list"></i></span>
                                Weekly Progress
                            </h3>
                            <div class="flex gap-2">
                                <span
                                    class="px-3 py-1 rounded-md bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-widest self-center border border-blue-100">
                                    {{ $team->reports->count() }} Submissions
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead
                                    class="bg-gray-50/80 text-[11px] font-black text-gray-500 uppercase tracking-widest border-b border-gray-200">
                                    <tr>
                                        <th class="px-8 py-5 text-left">Week</th>
                                        <th class="px-8 py-5 text-left">Date</th>
                                        <th class="px-8 py-5 text-left">Title</th>
                                        <th class="px-8 py-5 text-center">Attachment</th>

                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse($team->reports as $report)
                                        <tr class="hover:bg-blue-50/20 transition-all duration-200 group">
                                            <td class="px-8 py-5">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="h-8 w-1 bg-blue-500 rounded-full group-hover:h-12 transition-all duration-300">
                                                    </div>
                                                    <span class="font-black text-gray-700 text-sm">Week
                                                        #{{ $report->week_number }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 text-xs font-bold text-gray-500">
                                                {{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('M d') : '-' }}
                                            </td>
                                            <td class="px-8 py-5">
                                                <p class="text-xs text-gray-600 w-48 truncate leading-relaxed group-hover:text-blue-900 transition-colors"
                                                    title="{{ $report->achievements }}">
                                                    {{ Str::limit($report->achievements, 50) }}
                                                </p>
                                            </td>
                                            <td class="px-8 py-5 text-center">
                                                @if ($report->file_path)
                                                    {{-- ✅ هنا استخدمنا راوت الستاف الجديد --}}
                                                    <a href="{{ route('staff.view_attachment', ['path' => $report->file_path]) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm hover:shadow-lg transform hover:scale-110"
                                                        title="Download PDF Report">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @else
                                                    <span class="text-gray-300 text-lg" title="No file attached"><i
                                                            class="fas fa-minus"></i></span>
                                                @endif
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                                                <i class="fas fa-folder-open text-5xl mb-4 text-gray-200 block"></i>
                                                <p class="text-xs font-bold uppercase tracking-wide">No reports submitted
                                                    yet.
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 🔵 RIGHT COLUMN: DEFENSE & STATS (4 Columns) --}}
                <div class="xl:col-span-4" id="defense-section">
                    <div class="sticky top-28 space-y-8">

                        {{-- Gold Defense Card --}}
                        <div
                            class="bg-slate-900 rounded-[3rem] p-1.5 shadow-2xl overflow-hidden group border border-slate-800 transition-transform duration-500 hover:scale-[1.01] hover:shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
                            <div class="bg-slate-900 rounded-[2.8rem] p-8 relative overflow-hidden h-full">
                                {{-- Background Effects --}}
                                <div
                                    class="absolute -top-10 -left-10 w-48 h-48 bg-[#D4AF37] opacity-10 rounded-full blur-[60px] group-hover:opacity-20 transition-opacity">
                                </div>
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80">
                                </div>
                                <div
                                    class="absolute bottom-0 right-0 p-4 opacity-5 rotate-12 pointer-events-none transform transition-transform group-hover:scale-110">
                                    <i class="fas fa-calendar-check text-9xl text-white"></i>
                                </div>

                                <div class="relative z-10 text-center">
                                    <h3
                                        class="text-[10px] font-black uppercase tracking-[0.4em] text-[#D4AF37] mb-8 flex items-center justify-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-[#D4AF37] animate-ping"></span> Project
                                        Discussion
                                    </h3>

                                    @if ($team->defense_date)
                                        <div class="space-y-6 animate-premium">
                                            <div
                                                class="inline-block px-6 py-2 rounded-full bg-[#D4AF37]/10 border border-[#D4AF37]/30 text-[#D4AF37] text-[10px] font-bold uppercase tracking-widest shadow-[0_0_20px_rgba(212,175,55,0.25)]">
                                                <i class="fas fa-check-circle mr-1"></i> Scheduled & Verified
                                            </div>

                                            <div class="py-8 border-b border-white/5 border-t border-white/5">
                                                <p
                                                    class="text-7xl font-black text-white font-serif tracking-tighter mb-2 shadow-gold text-shadow scale-100 group-hover:scale-110 transition-transform duration-500">
                                                    {{ \Carbon\Carbon::parse($team->defense_date)->format('d') }}
                                                </p>
                                                <p class="text-xl font-bold text-gray-400 uppercase tracking-[0.3em]">
                                                    {{ \Carbon\Carbon::parse($team->defense_date)->format('F Y') }}
                                                </p>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div
                                                    class="bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10 hover:border-white/30 transition-colors group/item hover:bg-white/10">
                                                    <i
                                                        class="far fa-clock text-white mb-2 text-xl block group-hover/item:scale-110 transition-transform text-[#D4AF37]"></i>
                                                    <span class="text-xs font-mono text-white font-bold">
                                                        {{ \Carbon\Carbon::parse($team->defense_date)->format('h:i A') }}
                                                    </span>
                                                </div>

                                                <div
                                                    class="bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10 hover:border-white/30 transition-colors group/item hover:bg-white/10">
                                                    <i
                                                        class="fas fa-map-marker-alt text-white mb-2 text-xl block group-hover/item:scale-110 transition-transform text-[#D4AF37]"></i>
                                                    <span
                                                        class="text-[10px] text-white font-black truncate block uppercase">
                                                        {{ $team->defense_location }}
                                                    </span>
                                                </div>
                                            </div>

                                            <button
                                                onclick="document.getElementById('defenseForm').classList.toggle('hidden')"
                                                class="mt-6 text-[10px] font-black text-gray-500 hover:text-white uppercase tracking-widest transition-all hover:bg-white/5 py-3 px-4 rounded-xl w-full border border-transparent hover:border-white/10">
                                                <i class="fas fa-edit mr-1"></i> Reschedule Event
                                            </button>
                                        </div>
                                    @else
                                        <div
                                            class="py-12 border-2 border-dashed border-slate-700 rounded-[2rem] bg-slate-800/30">
                                            <div
                                                class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                                                <i class="fas fa-calendar-plus text-3xl text-slate-600"></i>
                                            </div>
                                            <p class="text-slate-500 text-sm font-black uppercase tracking-widest">Calendar
                                                Empty</p>
                                            <p class="text-slate-600 text-[10px] mt-2">Initialize the final defense
                                                schedule
                                                below</p>
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div
                                            class="bg-red-500/20 border border-red-500 text-red-200 p-4 rounded-xl mb-4 text-xs mt-4">
                                            <ul class="list-disc list-inside">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- Schedule Form --}}
                                    <form id="defenseForm" action="{{ route('staff.team.defense', $team->id) }}"
                                        method="POST" @submit="loading = true"
                                        class="{{ $team->defense_date ? 'hidden' : '' }} mt-10 pt-10 border-t border-white/5 space-y-5 animate-premium">
                                        @csrf
                                        <div class="text-left">
                                            <label
                                                class="text-[9px] uppercase text-gray-500 font-black mb-2 block ml-2 tracking-widest">Discussion
                                                Date</label>
                                            <input type="datetime-local" name="defense_date" required
                                                class="w-full bg-black/40 border border-slate-700 text-white text-xs rounded-xl p-4 focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] outline-none transition-all font-mono hover:bg-black/60 placeholder-gray-600">
                                        </div>
                                        <div class="text-left">
                                            <label
                                                class="text-[9px] uppercase text-gray-500 font-black mb-2 block ml-2 tracking-widest">Location
                                                / Hall</label>
                                            <input type="text" name="defense_location"
                                                placeholder="ex: Main Hall, Room 303" required
                                                class="w-full bg-black/40 border border-slate-700 text-white text-xs rounded-xl p-4 focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] outline-none transition-all hover:bg-black/60 placeholder-gray-600">
                                        </div>
                                        <button type="submit"
                                            class="w-full bg-gradient-to-r from-[#D4AF37] via-[#fcf6ba] to-[#b38728] text-slate-900 py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-2xl transform hover:scale-[1.03] active:scale-95 transition-all hover:shadow-[0_0_20px_rgba(212,175,55,0.4)]">
                                            Confirm Schedule
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================= --}}
                        {{-- 📊 Real-Time Execution Summary (Dynamic) --}}
                        {{-- ============================================= --}}

                        @php
                            // Logic Preserved Exactly as requested
                            $teamMeetingIds = \App\Models\Meeting::where('team_id', $team->id)->pluck('id');
                            $totalAttendanceRecords = \App\Models\Attendance::whereIn(
                                'meeting_id',
                                $teamMeetingIds,
                            )->count();
                            $presentRecords = \App\Models\Attendance::whereIn('meeting_id', $teamMeetingIds)
                                ->where('status', 1)
                                ->count();

                            $reliability =
                                $totalAttendanceRecords > 0
                                    ? round(($presentRecords / $totalAttendanceRecords) * 100, 1)
                                    : 100;

                            $reliabilityColor =
                                $reliability >= 85
                                    ? 'text-emerald-600'
                                    : ($reliability >= 60
                                        ? 'text-yellow-600'
                                        : 'text-red-600');
                            $reliabilityBarColor =
                                $reliability >= 85
                                    ? 'from-emerald-500 to-teal-500'
                                    : ($reliability >= 60
                                        ? 'from-yellow-500 to-orange-500'
                                        : 'from-red-500 to-pink-500');

                            $phaseMap = [
                                'planning' => 10,
                                'analysis' => 25,
                                'design' => 45,
                                'implementation' => 65,
                                'testing' => 80,
                                'ready_for_defense' => 90,
                                'completed' => 100,
                            ];
                            $completion = $phaseMap[$team->project_phase ?? 'planning'] ?? 5;
                        @endphp

                        <div
                            class="glass-card rounded-[2.5rem] p-8 border-l-4 border-blue-500 bg-white shadow-sm hover:shadow-md transition-shadow">
                            <h4
                                class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-100 pb-2">
                                Execution Metrics</h4>

                            <div class="space-y-8">
                                {{-- Reliability Meter --}}
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-gray-600">Team Reliability</span>
                                        <span
                                            class="text-xs font-black {{ $reliabilityColor }}">{{ $reliability }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-gradient-to-r {{ $reliabilityBarColor }} h-full shadow-lg transition-all duration-1000 ease-out"
                                            style="width: {{ $reliability }}%"></div>
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-1">Based on attendance consistency</p>
                                </div>

                                {{-- Completion Meter --}}
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-gray-600">Phase Completion</span>
                                        <span class="text-xs font-black text-blue-600">{{ $completion }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-full shadow-lg shadow-blue-200 transition-all duration-1000 ease-out"
                                            style="width: {{ $completion }}%"></div>
                                    </div>
                                    <p
                                        class="text-[10px] text-gray-400 mt-2 uppercase text-right font-black tracking-wider">
                                        Current: <span
                                            class="text-blue-500">{{ str_replace('_', ' ', $team->project_phase ?? 'Planning') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="h-10 border-t border-gray-200/50"></div> {{-- Spacer --}}

            {{--
            ██████████████████████████████████████████████████████████████████████████
            🤝 SECTION 3: SUPERVISION & MEETINGS (قسم الاجتماعات)
            ██████████████████████████████████████████████████████████████████████████
            --}}
            <div id="meetings-section" class="grid grid-cols-1 lg:grid-cols-2 gap-12 animate-premium delay-300">

                {{-- 3.A: Meetings List --}}
                <div class="space-y-8">
                    <div class="flex justify-between items-end border-b border-gray-200 pb-4">
                        <div>
                            <h3 class="text-3xl font-black text-slate-900 flex items-center gap-4 tracking-tight">
                                <span
                                    class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-xl">
                                    <i class="fas fa-comments-alt text-xl"></i>
                                </span>
                                Meeting With Team
                            </h3>
                            <p class="text-xs text-gray-400 font-bold uppercase mt-2 tracking-widest pl-1">Manage Meetings
                                &

                                Action-required requests</p>
                        </div>
                        <button onclick="document.getElementById('supervisionHistoryModal').classList.remove('hidden')"
                            class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 text-[10px] font-black uppercase hover:bg-slate-900 hover:text-white transition-all hover:shadow-lg border border-slate-200">
                            <i class="fas fa-history mr-2"></i> View History
                        </button>
                    </div>

                    @php
                        $meetings = \App\Models\Meeting::where('team_id', $team->id)
                            ->where('type', 'supervision')
                            ->whereIn('status', ['pending', 'scheduled', 'confirmed'])
                            ->orderBy('status', 'desc')
                            ->orderBy('meeting_date', 'asc')
                            ->get();
                    @endphp

                    <div class="space-y-8">
                        @forelse($meetings as $meeting)
                            @if ($meeting->status == 'pending')
                                {{-- 🟧 PENDING CARD --}}
                                <div
                                    class="glass-card rounded-[2.5rem] p-8 border-l-8 border-orange-400 relative overflow-hidden group hover:border-orange-500 bg-orange-50/20">
                                    <div
                                        class="absolute top-0 right-0 p-6 opacity-5 rotate-12 group-hover:rotate-0 transition-transform duration-700">
                                        <i class="fas fa-hourglass-start text-9xl text-orange-900"></i>
                                    </div>

                                    <div class="flex justify-between items-start mb-6">
                                        <div class="flex gap-6">
                                            <div
                                                class="w-16 h-16 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-500 text-2xl shadow-inner border border-orange-100">
                                                @if ($meeting->mode == 'online')
                                                    <i class="fas fa-video"></i>
                                                @else
                                                    <i class="fas fa-handshake"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-black text-slate-800 text-xl leading-tight">
                                                    {{ $meeting->topic }}
                                                </h4>
                                                <p class="text-xs text-slate-400 font-mono mt-1 font-bold">
                                                    <i class="far fa-calendar-star mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('D, d M - h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span
                                            class="px-4 py-1.5 bg-orange-100 text-orange-700 text-[9px] font-black uppercase tracking-widest rounded-full animate-pulse shadow-sm">
                                            Request Pending
                                        </span>
                                    </div>

                                    <div
                                        class="bg-white/60 p-5 rounded-2xl border border-orange-100 mb-8 backdrop-blur-sm">
                                        <p class="text-[9px] font-black text-orange-400 uppercase mb-2 tracking-widest">
                                            Student
                                            Narrative:</p>
                                        <p class="text-sm text-slate-700 font-medium italic leading-relaxed">
                                            "{{ $meeting->description ?? 'Communication briefing not provided.' }}"
                                        </p>
                                    </div>

                                    <form action="{{ route('staff.meeting.respond', $meeting->id) }}" method="POST"
                                        class="space-y-6" @submit="loading = true">
                                        @csrf
                                        @if ($meeting->mode == 'online')
                                            <div class="relative group/input">
                                                <label
                                                    class="text-[10px] font-black text-slate-400 uppercase mb-2 block ml-2">Secure
                                                    Meeting URL (Zoom/Teams)</label>
                                                <input type="url" name="meeting_link" placeholder="https://..."
                                                    required
                                                    class="w-full bg-white border border-slate-200 rounded-xl p-4 text-xs focus:ring-4 focus:ring-orange-100 focus:border-orange-400 outline-none transition-all shadow-sm group-hover/input:border-orange-300">
                                            </div>
                                        @else
                                            <div class="relative group/input">
                                                <label
                                                    class="text-[10px] font-black text-slate-400 uppercase mb-2 block ml-2">On-Site
                                                    Logistics (Room/Hall)</label>
                                                <input type="text" name="location"
                                                    placeholder="e.g. Executive Suite 402" required
                                                    class="w-full bg-white border border-slate-200 rounded-xl p-4 text-xs focus:ring-4 focus:ring-orange-100 focus:border-orange-400 outline-none transition-all shadow-sm group-hover/input:border-orange-300">
                                            </div>
                                        @endif

                                        <div class="flex gap-4 pt-2">
                                            <button type="submit" name="status" value="confirmed"
                                                class="flex-1 bg-slate-900 hover:bg-black text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover:shadow-2xl transform active:scale-95 transition-all flex items-center justify-center gap-2">
                                                <i class="fas fa-check"></i> Authorize
                                            </button>
                                            <button type="submit" name="status" value="rejected" formnovalidate
                                                class="flex-1 bg-white border border-red-100 text-red-500 hover:bg-red-50 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-2">
                                                <i class="fas fa-times"></i> Decline
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @elseif($meeting->status == 'scheduled' || $meeting->status == 'confirmed')
                                {{-- 🟩 CONFIRMED/ACTIVE CARD --}}
                                <div
                                    class="glass-card rounded-[2.5rem] p-8 border-l-8 border-emerald-500 bg-emerald-50/10 relative overflow-hidden ring-4 ring-emerald-500/5 hover:shadow-2xl transition-shadow">
                                    <div
                                        class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-500 to-teal-400 shadow-lg">
                                    </div>

                                    <div class="flex justify-between items-start mb-8">
                                        <div class="flex gap-6">
                                            <div
                                                class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl shadow-inner animate-pulse">
                                                <i class="fas fa-clock-rotate-left"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-black text-slate-900 text-xl tracking-tight">
                                                    {{ $meeting->topic }}
                                                </h4>
                                                @if ($meeting->meeting_link)
                                                    <a href="{{ $meeting->meeting_link }}" target="_blank"
                                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white text-[10px] font-black uppercase mt-3 hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200 transform hover:-translate-y-0.5">
                                                        <i class="fas fa-external-link-alt"></i> Join Live Session
                                                    </a>
                                                @else
                                                    <p
                                                        class="text-xs text-slate-500 font-bold mt-2 bg-white/50 px-3 py-1 rounded-lg inline-block border border-white">
                                                        <i class="fas fa-map-marker-check text-emerald-500 mr-1"></i>
                                                        {{ $meeting->location }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <span
                                            class="px-5 py-2 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest rounded-xl animate-pulse shadow-lg shadow-emerald-200/50">
                                            Active
                                        </span>
                                    </div>

                                    <div class="h-px bg-slate-200/50 w-full mb-8"></div>

                                    {{-- Attendance Form --}}
                                    <form action="{{ route('staff.meeting.end', $meeting->id) }}" method="POST"
                                        @submit="loading = true">
                                        @csrf
                                        <h3
                                            class="font-black text-slate-800 text-[10px] uppercase tracking-[0.2em] mb-4 ml-1 flex items-center gap-2">
                                            <i class="fas fa-clipboard-user text-emerald-500"></i> Attendance Verification
                                        </h3>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-8">
                                            @foreach ($team->members as $member)
                                                <div
                                                    class="flex items-center justify-between bg-white/60 backdrop-blur-sm p-4 rounded-2xl border border-slate-100 hover:border-emerald-200 transition-all shadow-sm group/att">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-black shadow-md">
                                                            {{ substr($member->user->name, 0, 1) }}
                                                        </div>
                                                        <span
                                                            class="text-[11px] font-black text-slate-700">{{ $member->user->name }}</span>
                                                    </div>
                                                    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl">
                                                        <label class="cursor-pointer group/radio">
                                                            <input type="radio"
                                                                name="attendance[{{ $member->user_id }}]" value="present"
                                                                checked class="peer sr-only">
                                                            <span
                                                                class="block px-3 py-1.5 rounded-lg text-[9px] font-black text-slate-400 peer-checked:bg-emerald-600 peer-checked:text-white transition-all shadow-sm">P</span>
                                                        </label>
                                                        <label class="cursor-pointer group/radio">
                                                            <input type="radio"
                                                                name="attendance[{{ $member->user_id }}]" value="absent"
                                                                class="peer sr-only">
                                                            <span
                                                                class="block px-3 py-1.5 rounded-lg text-[9px] font-black text-slate-400 peer-checked:bg-red-600 peer-checked:text-white transition-all shadow-sm">A</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="submit"
                                            class="w-full bg-white border-2 border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all shadow-xl hover:shadow-emerald-200 flex items-center justify-center gap-3 group/submit">
                                            <i
                                                class="fas fa-shield-check group-hover/submit:scale-110 transition-transform"></i>
                                            Finalize & Synchronize Records
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @empty
                            <div
                                class="py-24 text-center glass-card rounded-[3rem] border-2 border-dashed border-slate-300 opacity-80 hover:opacity-100 transition-opacity">
                                <div
                                    class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                                    <i class="fas fa-clipboard-check text-4xl text-slate-300"></i>
                                </div>
                                <p class="text-slate-800 font-black text-lg tracking-tight uppercase">Operational Clearance
                                </p>
                                <p class="text-slate-500 text-xs font-bold mt-2">No pending supervision requests found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 3.B: Final Deliverables & Grading --}}
                <div class="space-y-8" id="artifacts-section">
                    <div class="flex items-center gap-4 mb-2">
                        <span
                            class="w-12 h-12 rounded-2xl bg-[#D4AF37] text-slate-900 flex items-center justify-center shadow-xl shadow-yellow-200/50">
                            <i class="fas fa-gem text-xl"></i>
                        </span>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">Project Artifacts</h3>
                    </div>

                    @if ($team->final_book_file || $team->presentation_file || $team->defense_video_link)
                        <div
                            class="glass-card rounded-[3rem] p-2 bg-gradient-to-br from-white to-gray-50 animate-premium delay-300 shadow-2xl ring-1 ring-slate-900/5">
                            <div class="bg-white rounded-[2.8rem] overflow-hidden relative">
                                <div class="bg-slate-900 px-10 py-12 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-4 opacity-10 rotate-12">
                                        <i class="fas fa-graduation-cap text-9xl text-white"></i>
                                    </div>
                                    <div
                                        class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6 text-center md:text-left">
                                        <div>
                                            <h2
                                                class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#fcf6ba] via-[#bf953f] to-[#fcf6ba] uppercase tracking-[0.2em] drop-shadow-sm">
                                                Thesis Vault
                                            </h2>
                                            <p class="text-gray-400 text-[10px] mt-2 font-black tracking-widest uppercase">
                                                Verified Graduation Project Submissions
                                            </p>
                                        </div>
                                        @if ($team->is_fully_submitted)
                                            <div
                                                class="bg-[#D4AF37]/20 text-[#D4AF37] border border-[#D4AF37]/30 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest backdrop-blur-md shadow-2xl animate-pulse">
                                                <i class="fas fa-lock-alt mr-2"></i> Integrity Sealed
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-10 grid grid-cols-1 gap-6">
                                    {{-- Book --}}
                                    @if ($team->final_book_file)
                                        {{-- ✅✅✅ التعديل هنا: استخدام راوت الستاف --}}
                                        <a href="{{ route('staff.view_attachment', ['path' => $team->final_book_file]) }}"
                                            target="_blank"
                                            class="group flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-emerald-100 transition-all duration-500 hover:-translate-y-1">
                                            <div class="flex items-center gap-6">
                                                <div
                                                    class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-emerald-600 text-3xl shadow-sm group-hover:scale-110 group-hover:rotate-6 transition-transform">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-black text-slate-800 text-lg uppercase tracking-tight">
                                                        Final Thesis Book
                                                    </h4>
                                                    <span
                                                        class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Formal
                                                        Documentation (PDF)</span>
                                                </div>
                                            </div>
                                            <div
                                                class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center text-green-600 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0 shadow-md">
                                                <i class="fas fa-download text-lg"></i>
                                            </div>
                                        </a>
                                    @endif

                                    {{-- PPT --}}
                                    @if ($team->presentation_file)
                                        {{-- ✅✅✅ التعديل هنا: استخدام راوت الستاف --}}
                                        <a href="{{ route('staff.view_attachment', ['path' => $team->presentation_file]) }}"
                                            target="_blank"
                                            class="group flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-orange-100 transition-all duration-500 hover:-translate-y-1">
                                            <div class="flex items-center gap-6">
                                                <div
                                                    class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-orange-600 text-3xl shadow-sm group-hover:scale-110 group-hover:-rotate-6 transition-transform">
                                                    <i class="fas fa-presentation-screen"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-black text-slate-800 text-lg uppercase tracking-tight">
                                                        Executive Presentation
                                                    </h4>
                                                    <span
                                                        class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Visual
                                                        Defense Slides (PPTX)</span>
                                                </div>
                                            </div>
                                            <div
                                                class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center text-orange-600 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0 shadow-md">
                                                <i class="fas fa-download text-lg"></i>
                                            </div>
                                        </a>
                                    @endif

                                    {{-- Video --}}
                                    @if ($team->defense_video_link)
                                        <a href="{{ $team->defense_video_link }}" target="_blank"
                                            class="group flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-red-100 transition-all duration-500 hover:-translate-y-1">
                                            <div class="flex items-center gap-6">
                                                <div
                                                    class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-red-600 text-3xl shadow-sm group-hover:scale-110 transition-transform">
                                                    <i class="fab fa-youtube"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-black text-slate-800 text-lg uppercase tracking-tight">
                                                        Project
                                                        Demonstration</h4>
                                                    <span
                                                        class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Video
                                                        Stream (External)</span>
                                                </div>
                                            </div>
                                            <div
                                                class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center text-red-600 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0 shadow-md">
                                                <i class="fas fa-play text-lg"></i>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div
                            class="glass-card rounded-[3rem] p-20 text-center border-2 border-dashed border-slate-300 hover:border-slate-400 transition-colors">
                            <div
                                class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl text-slate-200">
                                <i class="fas fa-treasure-chest text-5xl"></i>
                            </div>
                            <h4 class="text-slate-800 font-black text-2xl mb-3 tracking-tighter">Vault is Empty</h4>
                            <p class="text-slate-400 text-sm max-w-sm mx-auto font-medium">Final deliverables are being
                                prepared
                                by the team. Once submitted, they will undergo digital verification.</p>
                        </div>
                    @endif

                    {{-- 🏆 PROJECT GRADING CARD --}}
                    <div
                        class="bg-white rounded-[2.5rem] shadow-xl p-8 relative overflow-hidden border border-gray-100 mt-6 transform hover:scale-[1.02] transition-transform duration-300 ring-4 ring-purple-500/5">
                        <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                            <i class="fas fa-award text-8xl text-purple-600"></i>
                        </div>

                        <h3
                            class="text-sm font-bold uppercase tracking-widest mb-6 flex items-center gap-3 text-gray-800 border-b border-gray-100 pb-3">
                            <i class="fas fa-award text-purple-600 text-lg"></i> Final Evaluation
                        </h3>

                        @if ($team->project_score)
                            {{-- ✅ Graded --}}
                            @php
                                $percentage = ($team->project_score / $team->project_max_score) * 100;
                                $gradeColor =
                                    $percentage >= 85
                                        ? 'text-green-600'
                                        : ($percentage >= 75
                                            ? 'text-blue-600'
                                            : ($percentage >= 65
                                                ? 'text-yellow-600'
                                                : 'text-red-600'));
                                $gradeBg =
                                    $percentage >= 85
                                        ? 'bg-green-100'
                                        : ($percentage >= 75
                                            ? 'bg-blue-100'
                                            : ($percentage >= 65
                                                ? 'bg-yellow-100'
                                                : 'bg-red-100'));
                            @endphp

                            <div class="text-center py-4">
                                <div
                                    class="inline-block px-4 py-1.5 rounded-full {{ $gradeBg }} {{ $gradeColor }} text-[10px] font-bold uppercase mb-4 shadow-sm">
                                    Graded Successfully
                                </div>

                                <div class="flex items-end justify-center gap-1 mb-2">
                                    <span
                                        class="text-6xl font-black text-gray-800 tracking-tighter">{{ $team->project_score }}</span>
                                    <span class="text-gray-400 text-lg font-bold mb-2">/
                                        {{ $team->project_max_score }}</span>
                                </div>

                                <div class="text-center mb-6">
                                    <span class="text-3xl font-bold {{ $gradeColor }}">
                                        {{ round($percentage, 1) }}%
                                    </span>
                                    <span
                                        class="text-xs text-gray-400 block mt-1 font-bold uppercase tracking-widest">Total
                                        Efficiency</span>
                                </div>

                                <button onclick="document.getElementById('gradingForm').classList.toggle('hidden')"
                                    class="text-xs text-gray-400 hover:text-purple-600 transition flex items-center justify-center gap-2 w-full py-2 hover:bg-purple-50 rounded-lg">
                                    <i class="fas fa-pen"></i> Edit Grade
                                </button>
                            </div>
                        @else
                            {{-- ❌ Not Graded --}}
                            <div class="text-center py-6">
                                <div
                                    class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce shadow-sm">
                                    <i class="fas fa-star-half-alt text-2xl text-purple-400"></i>
                                </div>
                                <p class="text-gray-400 text-xs font-bold uppercase tracking-wide">No grade assigned yet.
                                </p>
                                <p class="text-[10px] text-gray-400 mt-1">Set the final score for this project.</p>

                                <button onclick="document.getElementById('gradingForm').classList.remove('hidden')"
                                    class="mt-6 bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-xl text-xs font-bold transition shadow-lg shadow-purple-200 transform hover:scale-105">
                                    Set Grade Now
                                </button>
                            </div>
                        @endif

                        <form id="gradingForm" action="{{ route('staff.team.grade', $team->id) }}" method="POST"
                            class="{{ $team->project_score ? 'hidden' : 'hidden' }} mt-4 pt-4 border-t border-gray-100 animate-premium bg-gray-50/50 p-6 rounded-2xl"
                            @submit="loading = true">
                            @csrf
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 uppercase block mb-1">Score
                                        Obtained</label>
                                    <input type="number" step="0.5" name="project_score" placeholder="e.g. 120"
                                        required value="{{ $team->project_score }}"
                                        class="w-full bg-white border border-gray-300 rounded-xl p-3 text-sm focus:ring-purple-500 focus:border-purple-500 font-bold text-gray-700 outline-none shadow-sm">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-gray-500 uppercase block mb-1">Max
                                        Score</label>
                                    <input type="number" step="1" name="project_max_score" placeholder="e.g. 150"
                                        required value="{{ $team->project_max_score ?? 100 }}"
                                        class="w-full bg-white border border-gray-300 rounded-xl p-3 text-sm focus:ring-purple-500 focus:border-purple-500 font-bold text-gray-500 outline-none shadow-sm">
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl text-xs font-bold transition shadow-lg shadow-purple-200 flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> Save Final Result
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @include ('staff.modals_manage_team')

            {{--
            ██████████████████████████████████████████████████████████████████████████
            📜 SCRIPTS
            ██████████████████████████████████████████████████████████████████████████
            --}}
            <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
            <script>
                function openEndMeetingModal(meetingId) {
                    var form = document.getElementById('endMeetingForm');
                    form.action = "/staff/meeting/" + meetingId + "/end";
                    document.getElementById('endMeetingModal').classList.remove('hidden');
                }

                function closeModal(modalId) {
                    document.getElementById(modalId).classList.add('hidden');
                }

                // --- Keyboard Accessibility ---
                document.onkeydown = function(evt) {
                    evt = evt || window.event;
                    if (evt.keyCode == 27) { // Escape key
                        closeModal('endMeetingModal');
                        document.getElementById('membersModal').classList.add('hidden');
                        document.getElementById('supervisionHistoryModal').classList.add('hidden');
                    }
                };

                // --- Scroll Logic for Progress Bar ---
                window.onscroll = function() {
                    var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                    var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                    var scrolled = (winScroll / height) * 100;
                    document.getElementById("scroll-progress").style.width = scrolled + "%";
                };
            </script>
        @endsection
