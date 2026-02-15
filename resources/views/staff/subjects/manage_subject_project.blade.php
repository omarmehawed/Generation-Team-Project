@extends('layouts.staff')

@section('content')

    {{-- ========================================================= --}}
    {{-- 👑 SECTION 1: STYLES (THE ROYAL THEME v2.0) --}}
    {{-- ========================================================= --}}
    <style>
        /* 1. التدرج الذهبي للنصوص */
        .text-gold-gradient {
            background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: shimmer 6s linear infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        /* 2. تأثير الزجاج (Glassmorphism) */
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        /* 3. بادجات الحالة (Status Badges) */
        .status-badge {
            @apply px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border flex items-center gap-1;
        }

        .status-submitted {
            @apply bg-green-50 text-green-600 border-green-200;
        }

        .status-graded {
            @apply bg-blue-50 text-blue-600 border-blue-200;
        }

        .status-pending {
            @apply bg-gray-50 text-gray-400 border-gray-200;
        }

        /* 4. أنيميشن الدخول */
        .animate-fade-in-up {
            animation: fadeInUp 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* 5. ستايلات المودال (Modal Fixes) */
        .staff-modal-overlay {
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
        }

        .staff-modal-content {
            transform: scale(0.95) translateY(20px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .staff-modal-content.active {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        /* منع السكرول للخلفية */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 15px;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen animate-fade-in-up" x-data="{ selectedTeams: [] }">

        {{-- 🧭 Breadcrumb --}}
        <nav class="flex mb-8 text-gray-400 text-xs font-bold uppercase tracking-widest">
            <a href="{{ route('subjects.index') }}" class="hover:text-[#D4AF37] transition">My Courses</a>
            <span class="mx-3 text-gray-600">/</span>
            <span class="text-gray-800">{{ $course->name }}</span>
        </nav>

        {{-- ========================================================= --}}
        {{-- 👑 SECTION 2: COMMAND CENTER (الهيدر والإحصائيات) --}}
        {{-- ========================================================= --}}
        <div
            class="relative bg-[#0f0f0f] rounded-[2.5rem] p-8 md:p-12 mb-16 shadow-2xl overflow-hidden group border border-gray-800">
            {{-- الخلفية --}}
            <div
                class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5 mix-blend-overlay">
            </div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-[#D4AF37]/10 rounded-full blur-[100px] -mr-20 -mt-20"></div>

            <div class="relative z-10 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-8">
                {{-- تفاصيل الكورس --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <span
                            class="bg-[#D4AF37] text-black text-[10px] font-black px-3 py-1 rounded-lg uppercase tracking-widest shadow-[0_0_15px_rgba(212,175,55,0.4)]">
                            {{ $course->code }}
                        </span>
                        @if ($project->deadline && \Carbon\Carbon::now()->gt($project->deadline))
                            <span
                                class="text-red-500 text-xs font-bold flex items-center gap-1 bg-red-500/10 px-2 py-1 rounded-md border border-red-500/20"><i
                                    class="fas fa-lock"></i> Locked</span>
                        @else
                            <span
                                class="text-green-500 text-xs font-bold flex items-center gap-1 bg-green-500/10 px-2 py-1 rounded-md border border-green-500/20"><i
                                    class="fas fa-lock-open"></i> Open</span>
                        @endif
                    </div>

                    <div>
                        <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter leading-tight">
                            {{ $course->name }}
                        </h1>
                        <p class="text-gold-gradient text-xl font-bold uppercase tracking-widest mt-1">Project Manager
                            Dashboard</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-400 font-medium pt-2">
                        <span class="flex items-center gap-2"><i class="fas fa-users text-gray-600"></i>
                            {{ $project->teams->count() }} Teams Registered</span>
                        <span class="flex items-center gap-2"><i class="fas fa-clock text-gray-600"></i> Deadline: <span
                                class="text-gray-200">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M, h:i A') : 'Not Set' }}</span></span>
                    </div>
                </div>

                {{-- أزرار التحكم والإحصائيات --}}
                <div class="flex flex-col sm:flex-row gap-4 w-full xl:w-auto">
                    {{-- الإحصائيات --}}
                    <div class="flex gap-3 bg-white/5 p-1.5 rounded-2xl border border-white/10 backdrop-blur-md">
                        <div
                            class="px-6 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-center min-w-[100px] flex flex-col justify-center">
                            <span
                                class="block text-3xl font-black text-green-400 leading-none mb-1">{{ $project->teams->whereNotNull('submission_path')->count() }}</span>
                            <span class="text-[9px] text-green-200/70 uppercase font-bold tracking-widest">Submitted</span>
                        </div>
                        <div
                            class="px-6 py-3 rounded-xl bg-blue-500/10 border border-blue-500/20 text-center min-w-[100px] flex flex-col justify-center">
                            <span
                                class="block text-3xl font-black text-blue-400 leading-none mb-1">{{ $project->teams->whereNotNull('project_score')->count() }}</span>
                            <span class="text-[9px] text-blue-200/70 uppercase font-bold tracking-widest">Graded</span>
                        </div>
                    </div>

                    {{-- زر الإعدادات --}}
                    <button onclick="openModal('settingsModal')"
                        class="bg-[#D4AF37] hover:bg-[#c5a028] text-black px-8 py-4 rounded-2xl font-black text-sm shadow-[0_10px_30px_-10px_rgba(212,175,55,0.5)] transition-all transform hover:-translate-y-1 hover:shadow-[0_20px_40px_-10px_rgba(212,175,55,0.6)] flex items-center justify-center gap-3 h-auto">
                        <i class="fas fa-cog text-lg"></i> <span>Settings</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 👑 SECTION 3: TEAMS GRID (شبكة الفرق) --}}
        {{-- ========================================================= --}}

        <div class="flex justify-between items-end mb-8">
            <div>
                <h3 class="text-2xl font-black text-gray-900 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm"><i
                            class="fas fa-th-large"></i></span>
                    Registered Teams
                </h3>
                <p class="text-gray-400 text-sm mt-1 ml-11 font-medium">Manage submissions and grades for all student teams.
                </p>
            </div>

            {{-- Search Input --}}
            <div class="relative group">
                <input type="text" placeholder="Search team..."
                    class="pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-[#D4AF37] focus:border-transparent shadow-sm w-72 transition-all group-hover:shadow-md">
                <i
                    class="fas fa-search absolute left-4 top-3.5 text-gray-400 group-focus-within:text-[#D4AF37] transition-colors"></i>
            </div>
        </div>

        {{-- Grid Container --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            {{-- ⚠️ لاحظ: هنا بنستخدم $teams المتغير الجديد اللي جاي من الكنترولر --}}
            @forelse($teams as $team)
                <div
                    class="glass-panel rounded-[2rem] p-7 relative group hover:-translate-y-2 transition-all duration-500 hover:shadow-2xl bg-white border border-gray-100">
                    {{-- 🔥 Checkbox (New) 🔥 --}}
                    <div class="absolute top-5 right-5 z-20">
                        <input type="checkbox" value="{{ $team->id }}" x-model="selectedTeams"
                            class="w-5 h-5 text-[#175c53] bg-gray-100 border-gray-300 rounded focus:ring-[#D4AF37] focus:ring-2 cursor-pointer transition-transform transform hover:scale-110">
                    </div>
                    {{-- شريط الحالة الملون --}}
                    <div
                        class="absolute top-0 left-8 right-8 h-1 rounded-b-lg transition-colors duration-300
                                {{ $team->project_score ? 'bg-blue-500' : ($team->submission_path || $team->submission_link ? 'bg-green-500' : 'bg-gray-200') }}">
                    </div>

                    {{-- الهيدر: الاسم والكود --}}
                    <div class="flex justify-between items-start mb-6 mt-2">
                        <div>
                            <h4 class="font-black text-gray-800 text-xl leading-tight mb-1 group-hover:text-[#D4AF37] transition-colors truncate max-w-[150px]"
                                title="{{ $team->name }}">
                                {{ $team->name }}
                            </h4>
                            <span
                                class="text-[10px] font-bold tracking-widest text-gray-400 bg-gray-100 px-2 py-1 rounded uppercase">{{ $team->code }}</span>
                        </div>

                        {{-- حالة التسليم --}}
                        @if ($team->project_score)
                            <div class="text-right">
                                <span
                                    class="block text-3xl font-black text-blue-600 leading-none">{{ $team->project_score }}</span>
                                <span
                                    class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">/{{ $project->max_score ?? 100 }}
                                    Score</span>
                            </div>
                        @elseif($team->submission_path || $team->submission_link)
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wider border border-green-100">
                                <i class="fas fa-check-circle"></i> Received
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-50 text-gray-400 text-[10px] font-bold uppercase tracking-wider border border-gray-100">
                                <i class="fas fa-hourglass-half"></i> Pending
                            </span>
                        @endif
                    </div>

                    {{-- الأعضاء --}}
                    <div class="flex -space-x-3 overflow-hidden mb-8 py-2 pl-2">
                        @foreach ($team->members->take(4) as $member)
                            <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white shadow-sm transform hover:scale-110 hover:z-10 transition-transform duration-200 cursor-help object-cover bg-gray-100"
                                src="https://ui-avatars.com/api/?name={{ $member->user->name }}&background=random&color=fff&size=128"
                                title="{{ $member->user->name }}" alt="{{ $member->user->name }}">
                        @endforeach
                        @if ($team->members->count() > 4)
                            <div
                                class="h-10 w-10 rounded-full bg-gray-100 ring-2 ring-white flex items-center justify-center text-[10px] font-black text-gray-500 shadow-sm z-0">
                                +{{ $team->members->count() - 4 }}
                            </div>
                        @endif
                    </div>

                    {{-- الأزرار --}}
                    <div class="grid grid-cols-1">
                        <a href="{{ route('staff.team.view', $team->id) }}"
                            class="bg-gray-900 border border-gray-900 text-white hover:bg-white hover:text-gray-900 py-3 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all text-center flex items-center justify-center gap-2 group/btn shadow-lg shadow-gray-200">
                            <i class="fas fa-eye text-gray-400 group-hover/btn:text-gray-900 transition-colors"></i> View
                            Project
                        </a>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full text-center py-24 bg-white/50 rounded-[3rem] border-2 border-dashed border-gray-300">
                    <div
                        class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="fas fa-users-slash text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-gray-900 font-black text-xl mb-2">No Teams Registered Yet</h3>
                    <p class="text-gray-500 text-sm max-w-xs mx-auto">Once students form teams for this project, they will
                        appear here automatically.</p>
                </div>
            @endforelse
        </div>

        {{-- 🔥 Pagination Links 🔥 --}}
        @if ($teams->hasPages())
            <div class="mt-10 px-4 py-4 bg-white rounded-2xl border border-gray-100 shadow-sm flex justify-center">
                {{ $teams->links() }}
            </div>
        @endif

        {{-- 🔥 Floating Action Bar (Subject Projects) 🔥 --}}
        <div x-show="selectedTeams.length > 0" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-full opacity-0"
            class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-50 w-auto">

            <div
                class="bg-[#1e293b] text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-6 border border-slate-700/50 backdrop-blur-xl">

                {{-- Counter --}}
                <div class="flex items-center gap-3 border-r border-slate-600 pr-6">
                    <span
                        class="bg-[#D4AF37] text-black w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                        x-text="selectedTeams.length">0</span>
                    <span class="font-bold text-sm text-slate-200">Teams Selected</span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    <form id="exportSubjectForm" action="{{ route('staff.subject.teams.export') }}" method="POST"
                        class="m-0">
                        @csrf
                        <input type="hidden" name="file_name" id="exportFileName">
                        <input type="hidden" name="teams" :value="JSON.stringify(selectedTeams)">

                        <button type="button" onclick="askForName()"
                            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition-all shadow-lg hover:shadow-emerald-500/30">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </form>

                    <button @click="selectedTeams = []" class="text-slate-400 hover:text-white transition px-2 ml-2">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- 🛑 قفلة الـ DIV الرئيسي (x-data) --}}
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function askForName() {
            const today = new Date().toISOString().slice(0, 10);
            Swal.fire({
                title: 'Export Subject Teams',
                text: 'Enter a name for your file:',
                input: 'text',
                inputValue: `Subject_Teams_${today}`,
                showCancelButton: true,
                confirmButtonText: 'Download',
                confirmButtonColor: '#175c53',
                cancelButtonColor: '#d33',
                background: '#f8fafc',
                inputValidator: (value) => {
                    if (!value) return 'You need to write something!'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('exportFileName').value = result.value;
                    document.getElementById('exportSubjectForm').submit();
                }
            });
        }

        // ... (باقي دوال المودال القديمة سيبها زي ما هي)
    </script>
    </div>


    {{-- ========================================================= --}}
    {{-- 🚪 ROYAL MODALS --}}
    {{-- ========================================================= --}}

    {{-- 1. Settings Modal --}}
    <div id="settingsModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 staff-modal-overlay transition-opacity" onclick="closeModal('settingsModal')"></div>

        <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">

                <div
                    class="staff-modal-content relative w-full max-w-md transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl border-t-8 border-[#D4AF37]">

                    {{-- Header --}}
                    <div class="bg-gray-50 px-8 py-6 border-b border-gray-100 flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-gray-900 rounded-2xl flex items-center justify-center text-[#D4AF37] shadow-lg transform rotate-3">
                            <i class="fas fa-sliders-h text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight">Project Rules</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Configure Constraints</p>
                        </div>
                        <button onclick="closeModal('settingsModal')"
                            class="ml-auto w-8 h-8 rounded-full hover:bg-red-50 text-gray-400 hover:text-red-500 transition flex items-center justify-center">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('staff.project.deadline', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="px-8 py-8 space-y-6">
                            {{-- Leave Deadline --}}
                            <div class="group">
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1 group-focus-within:text-[#D4AF37] transition-colors">
                                    <i class="far fa-clock mr-1"></i> Leave Team Deadline
                                </label>
                                <input type="datetime-local" name="deadline" value="{{ $project->deadline }}"
                                    class="staff-input w-full bg-gray-50 rounded-xl p-4 text-sm font-bold text-gray-800 outline-none">
                                <p class="text-[10px] text-red-400 mt-2 font-medium ml-1">
                                    <i class="fas fa-info-circle"></i> Students cannot leave teams after this date.
                                </p>
                            </div>

                            {{-- Leave Deadline --}}
                            {{-- <div class="group">
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1 group-focus-within:text-red-500 transition-colors">
                                    <i class="fas fa-user-slash mr-1"></i> Leave Team Deadline
                                </label>
                                <div class="relative">
                                    <input type="datetime-local" name="leave_team_deadline"
                                        value="{{ $project->leave_team_deadline }}"
                                        class="staff-input w-full bg-red-50/30 border-red-100 rounded-xl p-4 text-sm font-bold text-gray-800 outline-none focus:border-red-500 focus:ring-red-200">
                                    <p class="text-[10px] text-red-400 mt-2 font-medium ml-1">
                                        <i class="fas fa-info-circle"></i> Students cannot leave teams after this date.
                                    </p>
                                </div> --}}
                            {{--
                            </div>
                        </div> --}}

                            {{-- Footer --}}
                            <div class="bg-gray-50 px-8 py-5 flex justify-end">
                                <button type="submit"
                                    class="bg-gray-900 text-[#D4AF37] py-3 px-8 rounded-xl font-bold text-sm uppercase tracking-wider shadow-lg hover:bg-black hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                                    <span>Save Changes</span> <i class="fas fa-check-circle"></i>
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- 2. Grading Modal --}}
    <div id="gradeModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 staff-modal-overlay transition-opacity" onclick="closeModal('gradeModal')"></div>

        <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">

                <div
                    class="staff-modal-content relative w-full max-w-lg transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl border border-gray-100">

                    {{-- Header --}}
                    <div
                        class="bg-gradient-to-r from-gray-900 to-gray-800 px-8 py-10 text-center relative overflow-hidden">
                        {{-- Decor --}}
                        <div class="absolute top-0 right-0 w-40 h-40 bg-[#D4AF37]/10 rounded-full blur-3xl -mr-10 -mt-10">
                        </div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl -ml-10 -mb-10">
                        </div>

                        <h3 class="text-3xl font-black text-white mb-2 relative z-10" id="gradeTeamName">Team Name</h3>
                        <div
                            class="inline-block bg-[#D4AF37] text-black text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest relative z-10 shadow-lg">
                            Submission Review
                        </div>

                        <button onclick="closeModal('gradeModal')"
                            class="absolute top-6 right-6 text-gray-500 hover:text-white transition bg-white/5 hover:bg-white/20 rounded-full w-10 h-10 flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-8">
                        {{-- Resources --}}
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <a id="gradeLinkBtn" href="#" target="_blank"
                                class="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:bg-gray-900 hover:border-gray-900 hover:text-white transition-all duration-300 group cursor-pointer h-full">
                                <i
                                    class="fab fa-github text-3xl text-gray-400 group-hover:text-white mb-3 transition-colors"></i>
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 group-hover:text-[#D4AF37]">Open
                                    Repo</span>
                            </a>

                            <a id="gradeFileBtn" href="#" target="_blank"
                                class="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:bg-blue-600 hover:border-blue-600 hover:text-white transition-all duration-300 group cursor-pointer h-full">
                                <i
                                    class="fas fa-file-download text-3xl text-gray-400 group-hover:text-white mb-3 transition-colors"></i>
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 group-hover:text-white">Get
                                    File</span>
                            </a>
                        </div>

                        <div class="relative flex py-2 items-center mb-8">
                            <div class="flex-grow border-t border-gray-100"></div>
                            <span
                                class="flex-shrink-0 mx-4 text-gray-300 text-xs font-black uppercase tracking-widest">Grading
                                Area</span>
                            <div class="flex-grow border-t border-gray-100"></div>
                        </div>

                        {{-- Form --}}
                        <form id="gradeForm" method="POST">
                            @csrf
                            <div class="flex flex-col items-center">
                                <div class="relative mb-8">
                                    <input type="number" step="0.5" name="project_score" id="gradeInput" required
                                        placeholder="00"
                                        class="w-48 text-center text-7xl font-black text-gray-900 bg-transparent border-b-4 border-gray-100 focus:border-[#D4AF37] outline-none transition-all placeholder-gray-100 py-2">
                                    <span class="absolute -right-8 bottom-5 text-xl font-bold text-gray-300">/ <span
                                            id="maxScoreDisplay">100</span></span>
                                </div>

                                <input type="hidden" name="project_max_score" id="maxScoreInput">

                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-bold uppercase tracking-widest shadow-xl shadow-green-200 hover:shadow-green-400 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                                    <span class="text-lg">Confirm Grade</span> <i
                                        class="fas fa-check-circle text-2xl"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('hidden');
                document.body.classList.add('modal-open'); // Prevent background scroll
                setTimeout(() => {
                    const content = el.querySelector('.staff-modal-content');
                    if (content) content.classList.add('active');
                }, 10);
            }
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) {
                const content = el.querySelector('.staff-modal-content');
                if (content) content.classList.remove('active');
                setTimeout(() => {
                    el.classList.add('hidden');
                    document.body.classList.remove('modal-open');
                }, 300);
            }
        }

        // إغلاق المودال عند الضغط على زر ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeModal('settingsModal');
                closeModal('gradeModal');
            }
        });

        function openGradeModal(teamId, teamName, link, file, maxScore, currentScore) {
            // Text Data
            document.getElementById('gradeTeamName').innerText = teamName;
            document.getElementById('maxScoreDisplay').innerText = maxScore;
            document.getElementById('maxScoreInput').value = maxScore;
            document.getElementById('gradeInput').value = currentScore || '';

            // Form Action
            document.getElementById('gradeForm').action = "/staff/team/" + teamId + "/grade";

            // Buttons Logic
            const linkBtn = document.getElementById('gradeLinkBtn');
            const fileBtn = document.getElementById('gradeFileBtn');

            // Handle Repo Link
            if (link) {
                linkBtn.href = link;
                linkBtn.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
                linkBtn.classList.add('hover:bg-gray-900', 'hover:border-gray-900', 'hover:text-white');
            } else {
                linkBtn.removeAttribute('href');
                linkBtn.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
                linkBtn.classList.remove('hover:bg-gray-900', 'hover:border-gray-900', 'hover:text-white');
            }

            // Handle File Link
            if (file) {
                fileBtn.href = file;
                fileBtn.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
                fileBtn.classList.add('hover:bg-blue-600', 'hover:border-blue-600', 'hover:text-white');
            } else {
                fileBtn.removeAttribute('href');
                fileBtn.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
                fileBtn.classList.remove('hover:bg-blue-600', 'hover:border-blue-600', 'hover:text-white');
            }

            openModal('gradeModal');
        }
    </script>



    {{-- SCRIPTS --}}
    <script>
        // الدوال الأساسية لفتح وإغلاق المودال مع تأثيرات الحركة
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('hidden');
                // تأخير بسيط عشان الأنيميشن يشتغل
                setTimeout(() => {
                    const content = el.querySelector('.staff-modal-content');
                    if (content) content.classList.add('active');
                }, 10);
            }
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) {
                const content = el.querySelector('.staff-modal-content');
                if (content) content.classList.remove('active');

                // استنى لحد ما الأنيميشن يخلص قبل ما تخفي العنصر
                setTimeout(() => {
                    el.classList.add('hidden');
                }, 300);
            }
        }

        // دالة تعبئة بيانات مودال الدرجات
        function openGradeModal(teamId, teamName, link, file, maxScore, currentScore) {
            // 1. البيانات النصية
            document.getElementById('gradeTeamName').innerText = teamName;
            document.getElementById('maxScoreDisplay').innerText = maxScore;
            document.getElementById('maxScoreInput').value = maxScore;
            document.getElementById('gradeInput').value = currentScore || '';

            // 2. تحديث رابط الفورم
            document.getElementById('gradeForm').action = "/staff/team/" + teamId + "/grade";

            // 3. التحكم في أزرار الملفات (تفعيل/تعطيل)
            const linkBtn = document.getElementById('gradeLinkBtn');
            const fileBtn = document.getElementById('gradeFileBtn');

            // Github Link Logic
            if (link) {
                linkBtn.href = link;
                linkBtn.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
            } else {
                linkBtn.removeAttribute('href');
                linkBtn.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
            }

            // File Download Logic
            if (file) {
                fileBtn.href = file;
                fileBtn.classList.remove('opacity-40', 'pointer-events-none', 'grayscale');
            } else {
                fileBtn.removeAttribute('href');
                fileBtn.classList.add('opacity-40', 'pointer-events-none', 'grayscale');
            }

            openModal('gradeModal');
        }
    </script>
@endsection
