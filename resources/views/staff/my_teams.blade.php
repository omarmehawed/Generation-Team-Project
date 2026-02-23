@extends('layouts.staff')

@section('content')

    {{--
    ██████████████████████████████████████████████████████████████████████████
    💎 PREMIUM STYLES & ANIMATIONS
    ██████████████████████████████████████████████████████████████████████████
    --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&family=Cairo:wght@400;700&display=swap');

        /* Base Settings */
        :root {
            --primary-teal: #175c53;
            --accent-gold: #D4AF37;
            --surface-light: #ffffff;
            --surface-dim: #f8fafc;
        }

        body {
            font-family: 'Outfit', 'Cairo', sans-serif;
            background-color: #f1f5f9;
        }

        /* --- Animations --- */
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .animate-enter {
            animation: fadeInScale 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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

        /* --- Hover Effects --- */
        .hover-card-premium {
            transition: all 0.4s ease;
        }

        .hover-card-premium:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(23, 92, 83, 0.15);
        }

        /* --- Custom Inputs --- */
        .glass-input {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
        }
    </style>

    {{-- عدل السطر ده عندك في بداية الصفحة --}}
    <div class="max-w-7xl mx-auto space-y-10 pb-20" x-data="{ selectedTeams: [] }">

        {{--
        ██████████████████████████████████████████████████████████████████████████
        👑 HEADER SECTION (LUXURY PANEL)
        ██████████████████████████████████████████████████████████████████████████
        --}}
        <div
            class="relative bg-[#175c53] rounded-[2.5rem] p-4 md:p-12 mb-10 overflow-hidden shadow-2xl border border-[#175c53]/50 animate-enter group">

            {{-- Background Effects --}}
            <div class="absolute inset-0 bg-gradient-to-br from-[#175c53] to-[#0f3f38]"></div>
            <div
                class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay">
            </div>

            {{-- Animated Orbs --}}
            <div
                class="absolute -right-20 -bottom-20 w-96 h-96 bg-[#D4AF37] rounded-full blur-[120px] opacity-20 pointer-events-none group-hover:opacity-30 transition-opacity duration-1000">
            </div>
            <div
                class="absolute -left-20 -top-20 w-72 h-72 bg-emerald-400 rounded-full blur-[100px] opacity-10 pointer-events-none">
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">

                {{-- Title & Subtitle --}}
                <div class="space-y-2">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/10 text-[#D4AF37] text-[10px] font-black uppercase tracking-[0.2em] mb-2 backdrop-blur-md">
                        <i class="fas fa-university"></i> Academic Year 2025
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
                        Final Project <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-[#D4AF37] to-[#fcf6ba]">Teams</span>
                    </h1>
                    <p class="text-slate-300 text-sm font-medium max-w-lg">
                        Monitor progress, approve proposals, and manage student groups efficiently from this command center.
                    </p>
                </div>

                {{--
                🔍 SEARCH & FILTER BAR
                --}}
                <form action="{{ route('staff.my_teams') }}" method="GET"
                    class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white/10 backdrop-blur-xl p-3 rounded-2xl border border-white/20 shadow-xl lg:min-w-[500px]">

                    {{-- 1. Search Bar Section --}}
                    <div class="relative flex-grow w-full md:w-auto group">
                        <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-4 z-10">
                            <i
                                class="fas fa-search text-white/40 group-focus-within:text-[#D4AF37] hover:text-[#D4AF37] transition-colors cursor-pointer"></i>
                        </button>
                        <input type="search" enterkeyhint="search" name="search" id="teamSearchInput"
                            value="{{ request('search') }}"
                            class="block w-full py-3.5 pl-12 pr-4 text-sm text-white bg-white/5 border border-white/10 rounded-xl placeholder-white/40 focus:ring-0 focus:border-[#D4AF37]/50 focus:bg-white/10 outline-none transition-all duration-300"
                            placeholder="Find Team, Project, or Leader..." autocomplete="off">
                    </div>

                    {{-- Divider --}}
                    <div class="hidden md:block w-px h-10 bg-gradient-to-b from-transparent via-white/20 to-transparent">
                    </div>

                    {{-- 2. Filters Section --}}
                    <div class="flex items-center gap-4 shrink-0 w-full md:w-auto">
                        <div class="relative group w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i
                                    class="fas fa-filter text-[10px] text-white/50 group-hover:text-[#D4AF37] transition-colors"></i>
                            </div>
                            <select name="year" onchange="this.form.submit()"
                                class="w-full appearance-none bg-white/5 text-white text-xs font-bold pl-9 pr-10 py-3.5 rounded-xl border border-white/10 focus:ring-0 focus:border-[#D4AF37] cursor-pointer hover:bg-white/10 transition-colors outline-none uppercase tracking-wide">
                                <option value="all" class="text-slate-900 bg-white">All Levels</option>
                                <option value="1" class="text-slate-900 bg-white" {{ request('year') == '1' ? 'selected' : '' }}>Year 1</option>
                                <option value="2" class="text-slate-900 bg-white" {{ request('year') == '2' ? 'selected' : '' }}>Year 2</option>
                                <option value="3" class="text-slate-900 bg-white" {{ request('year') == '3' ? 'selected' : '' }}>Year 3</option>
                                <option value="4" class="text-slate-900 bg-white" {{ request('year') == '4' ? 'selected' : '' }}>Year 4</option>
                            </select>
                            <i
                                class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-white/50 pointer-events-none group-hover:text-white transition-colors"></i>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{--
        ██████████████████████████████████████████████████████████████████████████
        ⚙️ PROJECT GLOBAL SETTINGS (DEADLINE)
        ██████████████████████████████████████████████████████████████████████████
        --}}
        @if (isset($teams) && $teams->count() > 0)
            <div
                class="relative bg-white rounded-[2.5rem] p-8 md:p-10 border border-slate-100 shadow-2xl overflow-hidden mt-12 animate-enter delay-200 group">

                {{-- Decorative Backgrounds --}}
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-purple-500/5 rounded-full blur-[80px] -mr-10 -mt-10 pointer-events-none group-hover:bg-purple-500/10 transition-colors">
                </div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-[#D4AF37]/5 rounded-full blur-[60px] pointer-events-none">
                </div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">

                    {{-- Text Content --}}
                    <div class="max-w-xl">
                        <h3 class="text-sm font-black text-purple-600 uppercase tracking-[0.2em] flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 rounded-full bg-purple-600 animate-ping"></span>
                            Action Required
                        </h3>
                        <h2 class="text-2xl font-black text-slate-800 mb-2">Team Formation Deadline</h2>
                        <p class="text-slate-500 text-sm">
                            Set the final date for team modifications. After this timestamp, all student groups will be
                            <strong class="text-purple-700">locked</strong> and no further members can be added or removed.
                        </p>
                    </div>

                    {{-- Form --}}
                    {{-- ملاحظة: تم الحفاظ على المتغيرات كما هي لعدم كسر اللوجيك --}}
                    <div class="w-full md:w-auto bg-slate-50 p-6 rounded-3xl border border-slate-200/60 shadow-inner">
                        <form action="{{ route('staff.project.deadline', $teams->first()->project->id ?? '') }}" method="POST"
                            class="flex flex-col sm:flex-row items-end gap-4">
                            @csrf
                            @method('PUT')

                            <div class="w-full sm:w-64">
                                <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block tracking-wider">Select
                                    Deadline Date</label>
                                <div class="relative group/input">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <i
                                            class="fas fa-calendar-alt text-slate-400 group-focus-within/input:text-purple-500 transition-colors"></i>
                                    </div>
                                    <input type="datetime-local" name="deadline"
                                        value="{{ isset($teams->first()->project) && $teams->first()->project->deadline ? \Carbon\Carbon::parse($teams->first()->project->deadline)->format('Y-m-d\TH:i') : '' }}"
                                        class="w-full bg-white border border-slate-200 rounded-xl py-3 pl-11 pr-4 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-purple-100 focus:border-purple-500 outline-none transition-all shadow-sm">
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full sm:w-auto px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-purple-200 transition-all transform hover:scale-105 active:scale-95 h-[42px] flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> Save Config
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{--
        ██████████████████████████████████████████████████████████████████████████
        📊 TEAMS GRID SECTION
        ██████████████████████████████████████████████████████████████████████████
        --}}
        @if ($teams->count() > 0)
            {{-- 1. شبكة الكروت --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-enter delay-100">

                @foreach ($teams as $team)
                    <div
                        class="bg-white rounded-[2rem] shadow-sm border border-slate-100 hover-card-premium group relative overflow-hidden flex flex-col h-full">
                        <div class="absolute top-4 right-4 z-20">
                            <input type="checkbox" :value="{{ $team->id }}" x-model="selectedTeams"
                                class="w-6 h-6 text-[#175c53] bg-gray-100 border-gray-300 rounded focus:ring-[#D4AF37] focus:ring-2 cursor-pointer transition-transform transform hover:scale-110">
                        </div>
                        {{-- Decorative Top Gradient --}}
                        <div
                            class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-transparent via-[#D4AF37] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        </div>

                        <div class="p-8 flex-1">
                            {{-- Card Header --}}
                            <div class="flex justify-between items-start mb-6">
                                {{-- Avatar / Logo with Glow --}}
                                <div class="relative">
                                    {{-- تأثير الوهج الخلفي (مشترك للاثنين) --}}
                                    <div
                                        class="absolute inset-0 bg-[#D4AF37] blur-lg opacity-0 group-hover:opacity-20 transition-opacity">
                                    </div>

                                    @if ($team->logo)
                                        {{-- ✅ حالة 1: لو فيه لوجو، هاته من الراوت المباشر (زي الداشبورد) --}}
                                        <img src="{{ route('final_project.logo', $team->id) . '?v=' . time() }}" alt="{{ $team->name }}"
                                            class="relative w-16 h-16 rounded-2xl object-cover border-2 border-slate-100 shadow-inner group-hover:shadow-xl group-hover:scale-110 transform transition-all duration-500 bg-white">
                                    @else
                                        {{-- 🅰️ حالة 2: مفيش لوجو اعرض أول حرف (الكود القديم) --}}
                                        <div
                                            class="relative w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-2xl font-black text-slate-300 group-hover:bg-[#175c53] group-hover:text-[#D4AF37] transition-all duration-500 shadow-inner group-hover:shadow-xl group-hover:scale-110 transform">
                                            {{ substr($team->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Status Badge --}}
                                @php
                                    $statusColor = match ($team->project_phase) {
                                        'Finished', 'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'Proposal' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        default => 'bg-blue-50 text-blue-700 border-blue-100',
                                    };
                                    $statusIcon = match ($team->project_phase) {
                                        'Finished', 'Completed' => 'fa-check-circle',
                                        'Proposal' => 'fa-file-signature',
                                        default => 'fa-spinner',
                                    };
                                @endphp
                                <span
                                    class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusColor }} shadow-sm">
                                    <i class="fas {{ $statusIcon }} mr-1"></i> {{ $team->project_phase ?? 'Active' }}
                                </span>
                            </div>

                            {{-- Project Info --}}
                            <div class="space-y-3">
                                <h3
                                    class="text-xl font-black text-slate-800 font-serif group-hover:text-[#175c53] transition-colors truncate tracking-tight">
                                    {{ $team->name }}
                                </h3>
                                <p
                                    class="text-xs text-slate-500 font-medium leading-relaxed line-clamp-2 h-10 border-l-2 border-slate-100 pl-3 group-hover:border-[#D4AF37] transition-colors">
                                    {{ $team->proposal_title ?? 'Project title pending submission.' }}
                                </p>
                            </div>

                            {{-- Divider --}}
                            <div
                                class="my-6 h-px w-full bg-gradient-to-r from-transparent via-slate-200 to-transparent group-hover:via-[#D4AF37]/50 transition-all">
                            </div>

                            {{-- Stats Row --}}
                            <div class="flex items-center gap-6 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2 group/stat">
                                    <i class="fas fa-crown text-[#D4AF37] group-hover/stat:scale-125 transition-transform"></i>
                                    <span
                                        class="group-hover:text-slate-700 transition">{{ Str::limit($team->leader->name ?? 'N/A', 12) }}</span>
                                </div>
                                <div class="flex items-center gap-2 group/stat">
                                    <i
                                        class="fas fa-users text-slate-300 group-hover:text-[#175c53] group-hover/stat:scale-125 transition-all"></i>
                                    <span class="group-hover:text-slate-700 transition">{{ $team->members->count() }}
                                        Members</span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Footer --}}
                        <div
                            class="p-4 bg-slate-50/50 border-t border-slate-100 grid grid-cols-2 gap-3 group-hover:bg-white transition-colors">
                            <a href="mailto:{{ $team->leader->email }}"
                                class="flex items-center justify-center gap-2 text-slate-500 hover:text-[#175c53] text-[10px] font-black uppercase tracking-wider py-3 rounded-xl hover:bg-emerald-50/50 transition border border-transparent hover:border-emerald-100">
                                <i class="far fa-envelope text-sm"></i> Contact
                            </a>
                            <a href="{{ route('staff.team.manage', $team->id) }}"
                                class="flex items-center justify-center gap-2 text-white bg-slate-900 hover:bg-[#175c53] text-[10px] font-black uppercase tracking-wider py-3 rounded-xl transition-all shadow-lg shadow-slate-200 hover:shadow-emerald-200 transform hover:-translate-y-1 group/btn">
                                Manage <i class="fas fa-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 🔥 2. قسم الصفحات (Pagination) 🔥 --}}
            @if ($teams->hasPages())
                <div
                    class="mt-12 px-4 py-4 bg-white rounded-2xl border border-slate-100 shadow-sm flex justify-center animate-enter delay-200">
                    {{ $teams->links() }}
                </div>
            @endif
        @else
            {{-- 🏜️ EMPTY STATES --}}
            @if (request('search'))
                <div
                    class="flex flex-col items-center justify-center py-20 px-4 text-center rounded-[2.5rem] bg-white border border-slate-100 shadow-sm mt-6 animate-enter">
                    <div class="bg-yellow-50 p-6 rounded-full mb-6 shadow-inner animate-pulse">
                        <i class="fas fa-search-minus text-4xl text-[#D4AF37]"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2 tracking-tight">No Matches Found</h3>
                    <p class="text-slate-500 max-w-md mx-auto mb-8 font-medium">
                        We searched everywhere but couldn't find any team matching <span
                            class="text-[#D4AF37] font-black bg-yellow-50 px-2 rounded">"{{ request('search') }}"</span>
                    </p>
                    <a href="{{ route('staff.my_teams') }}"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-slate-900 hover:bg-black text-white text-xs font-black uppercase tracking-widest rounded-full transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                        <i class="fas fa-undo"></i> Reset Filters
                    </a>
                </div>
            @else
                <div
                    class="flex flex-col items-center justify-center py-32 bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200 mt-6 group hover:border-[#D4AF37] transition duration-500 animate-enter">
                    <div
                        class="w-28 h-28 bg-slate-50 rounded-full flex items-center justify-center mb-8 group-hover:scale-110 transition duration-300 shadow-sm group-hover:shadow-md">
                        <i class="fas fa-folder-open text-5xl text-slate-300 group-hover:text-[#D4AF37] transition"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2">No Teams Assigned Yet</h3>
                    <p class="text-slate-500 text-sm font-medium">Once student proposals are approved, they will appear in
                        this
                        grid.</p>
                </div>
            @endif
        @endif
        {{-- 🔥 Floating Action Bar (Dark Style) 🔥 --}}
        <div x-show="selectedTeams.length > 0" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-full opacity-0"
            class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-50 w-[90%] md:w-auto max-w-lg">

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

                    {{--
                    ⚡ التركة هنا:
                    1. شيلنا زرار Submit المباشر.
                    2. ضيفنا id للفورم عشان نتحكم فيه.
                    3. ضيفنا input مخفي عشان يشيل الاسم الجديد.
                    4. عملنا زرار عادي (type="button") بينادي دالة JS.
                    --}}
                    <form id="exportForm" action="{{ route('staff.teams.export') }}" method="POST" class="m-0">
                        @csrf
                        {{-- ده الانبوت المخفي اللي هيشيل الاسم --}}
                        <input type="hidden" name="file_name" id="exportFileName">
                        <input type="hidden" name="teams" :value="JSON.stringify(selectedTeams)">

                        <button type="button" onclick="askForName()"
                            class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition-all shadow-lg hover:shadow-emerald-500/30">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </form>

                    {{-- زرار الإلغاء --}}
                    <button @click="selectedTeams = []" class="text-slate-400 hover:text-white transition px-2 ml-2">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
    </div>


    {{-- 🔥 Floating Export Bar 🔥 --}}

    {{--
    📜 SCRIPTS
    --}}

    {{-- استدعاء Alpine.js عشان التفاعل يشتغل --}}
    {{-- 1. استدعاء SweetAlert2 (عشان الـ Popup الشيك) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- 2. Alpine.js --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        // 🔥 دالة طلب الاسم
        function askForName() {
            // التاريخ النهاردة كاقتراح افتراضي
            const today = new Date().toISOString().slice(0, 10);

            Swal.fire({
                title: 'Export To Excel',
                text: 'Enter a name for your file:',
                input: 'text',
                inputValue: `Teams_Report_${today}`, // الاسم الافتراضي
                showCancelButton: true,
                confirmButtonText: 'Download',
                confirmButtonColor: '#175c53',
                cancelButtonColor: '#d33',
                background: '#f8fafc',
                customClass: {
                    title: 'text-slate-800 font-bold',
                    input: 'text-center font-bold text-slate-700'
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to write something!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // خد الاسم اللي اليوزر كتبه وحطه في الانبوت المخفي
                    document.getElementById('exportFileName').value = result.value;
                    // ابعت الفورم
                    document.getElementById('exportForm').submit();
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            // Enhanced Search Logic with Debounce prevention
            const searchInput = document.getElementById('teamSearchInput');

            if (searchInput) {
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();

                        // Visual Feedback
                        this.classList.add('opacity-50');

                        let query = this.value;
                        let url = new URL(window.location.href);

                        if (query) {
                            url.searchParams.set('search', query);
                        } else {
                            url.searchParams.delete('search');
                        }

                        window.location.href = url.toString();
                    }
                });
            }
        });
    </script>


@endsection