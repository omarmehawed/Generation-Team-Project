@extends('layouts.staff')

@section('content')

    {{-- ========================================================= --}}
    {{-- 👑 SECTION 1: ROYAL STYLES (ستايلات الفخامة) --}}
    {{-- ========================================================= --}}
    <style>
        /* أنيميشن الظهور */
        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* خلفيات زجاجية */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
        }

        /* تدرج النصوص الذهبية */
        .text-gold-gradient {
            background: linear-gradient(135deg, #b88a44 0%, #eecf88 50%, #b88a44 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: shimmer 4s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        /* Scrollbar مخصص للوحة الرصد */
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in-up">

        {{-- 🧭 Breadcrumb --}}
        <nav class="flex items-center justify-between mb-8">
            <div class="flex items-center text-xs font-bold uppercase tracking-widest text-gray-400">
                <a href="{{ url()->previous() }}" class="hover:text-[#D4AF37] transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <span class="mx-3 text-gray-300">/</span>
                <span class="text-gray-800">{{ $team->name }} Dashboard</span>
            </div>
            
            @if($team->project_score)
                <div class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border border-blue-100 shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> Graded
                </div>
            @endif
        </nav>

        {{-- ========================================================= --}}
        {{-- 🏷️ SECTION 2: HEADER (اسم التيم والهيدر) --}}
        {{-- ========================================================= --}}
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tighter mb-2 flex items-center gap-4">
                    <span class="w-16 h-16 bg-[#0f0f0f] rounded-2xl flex items-center justify-center text-[#D4AF37] text-2xl shadow-2xl shadow-[#D4AF37]/20">
                        <i class="fas fa-cube"></i>
                    </span>
                    <div>
                        {{ $team->name }}
                        <span class="block text-lg font-medium text-gray-400 mt-1 tracking-normal">Team Dashboard</span>
                    </div>
                </h1>
            </div>
        </div>
        <div class="flex items-center gap-3 mb-8">
        {{-- زرار عرض الأعضاء (الجديد) --}}
            <button onclick="openModal('staffTeamMembersModal')" 
                class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-xl font-bold shadow-sm hover:bg-gray-50 hover:text-black transition flex items-center gap-2">
                <i class="fas fa-users text-[#D4AF37]"></i> Members Info
            </button>
        </div>


        {{-- ========================================================= --}}
        {{-- 📊 MAIN LAYOUT GRID (التقسيمة الرئيسية) --}}
        {{-- ========================================================= --}}
        {{-- هنا غيرت ال Grid عشان يقسم الصفحة عمودين من البداية --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            {{-- ⬅️ Left Column: Submission & Tasks (2/3 width) --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- 📦 1. FINAL SUBMISSION CARD (كارت التسليم - نقلته هنا عشان يبقى عالشمال) --}}
                @if($team->submission_path || $team->submission_link)
                    <div class="bg-gradient-to-r from-[#0f0f0f] to-[#1a1a1a] rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl relative overflow-hidden group border border-gray-800">
                        
                        {{-- Decorative Elements --}}
                        <div class="absolute top-0 right-0 w-80 h-80 bg-[#D4AF37]/10 rounded-full blur-[80px] -mr-20 -mt-20"></div>
                        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-600/10 rounded-full blur-[80px] -ml-20 -mb-20"></div>

                        <div class="relative z-10 flex flex-col justify-between items-start gap-8">
                            <div class="w-full">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="px-3 py-1 bg-[#D4AF37] text-black text-[10px] font-black uppercase rounded-lg tracking-widest shadow-lg shadow-[#D4AF37]/40">
                                        Final Submission
                                    </span>
                                    <span class="text-gray-400 text-xs font-bold uppercase tracking-wider">
                                        <i class="far fa-clock mr-1"></i> {{ $team->updated_at->format('d M, h:i A') }}
                                    </span>
                                </div>
                                
                                <h3 class="text-3xl font-bold mb-4 text-white">Project Deliverables</h3>
                                
                                @if($team->submission_comment)
                                    <div class="bg-white/5 border-l-4 border-[#D4AF37] p-4 rounded-r-xl backdrop-blur-sm mb-6">
                                        <p class="text-gray-300 italic text-sm">"{{ $team->submission_comment }}"</p>
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm mb-6">No comments attached with this submission.</p>
                                @endif
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4 w-full">
                                @if($team->submission_link)
                                    <a href="{{ $team->submission_link }}" target="_blank" 
                                    class="group flex-1 px-4 py-4 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl text-sm font-bold transition-all flex items-center justify-center gap-3 backdrop-blur-md">
                                        <i class="fab fa-github text-2xl text-gray-400 group-hover:text-white transition-colors"></i> 
                                        <span>Repository</span>
                                    </a>
                                @endif

                                @if($team->submission_path)
                                    <a href="{{ Str::startsWith($team->submission_path, ['http://', 'https://']) ? $team->submission_path : asset('storage/' . $team->submission_path) }}" target="_blank" 
                                    class="flex-1 px-4 py-4 bg-[#D4AF37] hover:bg-[#c5a028] text-black rounded-2xl text-sm font-black uppercase tracking-wider transition-all shadow-[0_10px_30px_-10px_rgba(212,175,55,0.5)] hover:shadow-[0_20px_40px_-10px_rgba(212,175,55,0.6)] transform hover:-translate-y-1 flex items-center justify-center gap-3">
                                        <i class="fas fa-cloud-download-alt text-xl"></i> Download Files
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    {{-- حالة عدم التسليم --}}
                    <div class="bg-orange-50 rounded-[2.5rem] p-8 border-2 border-orange-100 border-dashed flex items-center gap-6">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-orange-400 text-3xl shadow-sm">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-orange-800">No Final Submission Yet</h3>
                            <p class="text-orange-600/70 text-sm font-medium mt-1">This team has not uploaded their final project deliverables.</p>
                        </div>
                    </div>
                @endif


                {{-- 📋 2. TASKS SECTION (قسم التاسكات - تحت كارت التسليم) --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-xl overflow-hidden">
                    <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-tasks text-orange-400"></i> Team Tasks Progress
                        </h3>
                        <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">{{ $team->tasks->count() }} Tasks</span>
                    </div>

                    <div class="p-8 space-y-4">
                        @forelse($team->tasks as $task)
                            @php
                                $isOverdue = $task->deadline && \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status != 'completed';
                            @endphp

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border transition-all hover:shadow-md
                                {{ $task->status == 'completed' ? 'bg-green-50/30 border-green-100' :
                                ($task->status == 'reviewing' ? 'bg-yellow-50/30 border-yellow-100' : 'bg-white border-gray-100') }}">

                                <div class="flex items-start gap-4">
                                    {{-- Status Icon --}}
                                    <div class="mt-1">
                                        @if($task->status == 'completed')
                                            <div class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-xs">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        @elseif($task->status == 'reviewing')
                                            <div class="w-6 h-6 rounded-full bg-yellow-400 text-white flex items-center justify-center text-xs animate-pulse">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 rounded-full border-2 border-gray-200 text-gray-300 flex items-center justify-center">
                                                <i class="fas fa-circle text-[6px]"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">{{ $task->title }}</p>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                            <span class="flex items-center gap-1 bg-gray-100 px-2 py-0.5 rounded text-gray-600">
                                                <i class="fas fa-user"></i> {{ $task->user->name }}
                                            </span>
                                            @if($task->deadline)
                                                <span class="{{ $isOverdue ? 'text-red-500 font-bold' : '' }}">
                                                    <i class="far fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($task->deadline)->format('M d') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- File Download Button --}}
                                @if($task->submission_value && ($task->status == 'reviewing' || $task->status == 'completed'))
                                    <a href="{{ $task->submission_type == 'link' ? $task->submission_value : (Str::startsWith($task->submission_value, ['http://', 'https://']) ? $task->submission_value : asset('storage/' . $task->submission_value)) }}"
                                        target="_blank"
                                        class="mt-2 sm:mt-0 px-3 py-1.5 bg-white border border-gray-200 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-600 hover:text-white transition shadow-sm flex items-center gap-2">
                                        <i class="{{ $task->submission_type == 'link' ? 'fas fa-link' : 'fas fa-file-download' }}"></i>
                                        View Work
                                    </a>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-400">
                                <i class="fas fa-clipboard-list text-3xl mb-2 opacity-50"></i>
                                <p class="text-sm">No tasks assigned yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div> {{-- End Left Column --}}


            {{-- 🎓 Right Column: Defense & Grading (Sticky Sidebar) --}}
            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    
                    {{-- 📅 1. Gold Defense Card (ميعاد المناقشة) --}}
                    <div class="bg-slate-900 rounded-[3rem] p-1.5 shadow-2xl overflow-hidden group border border-slate-800 transition-transform duration-500 hover:scale-[1.01] hover:shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
                        <div class="bg-slate-900 rounded-[2.8rem] p-8 relative overflow-hidden h-full">
                            {{-- Background Effects --}}
                            <div class="absolute -top-10 -left-10 w-48 h-48 bg-[#D4AF37] opacity-10 rounded-full blur-[60px] group-hover:opacity-20 transition-opacity"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80"></div>
                            <div class="absolute bottom-0 right-0 p-4 opacity-5 rotate-12 pointer-events-none transform transition-transform group-hover:scale-110">
                                <i class="fas fa-calendar-check text-9xl text-white"></i>
                            </div>

                            <div class="relative z-10 text-center">
                                <h3 class="text-[10px] font-black uppercase tracking-[0.4em] text-[#D4AF37] mb-8 flex items-center justify-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#D4AF37] animate-ping"></span> Project Discussion
                                </h3>

                                @if($team->defense_date)
                                    <div class="space-y-6 animate-premium">
                                        <div class="inline-block px-6 py-2 rounded-full bg-[#D4AF37]/10 border border-[#D4AF37]/30 text-[#D4AF37] text-[10px] font-bold uppercase tracking-widest shadow-[0_0_20px_rgba(212,175,55,0.25)]">
                                            <i class="fas fa-check-circle mr-1"></i> Scheduled & Verified
                                        </div>

                                        <div class="py-8 border-b border-t border-white/5">
                                            <p class="text-7xl font-black text-white font-serif tracking-tighter mb-2 shadow-gold text-shadow scale-100 group-hover:scale-110 transition-transform duration-500">
                                                {{ \Carbon\Carbon::parse($team->defense_date)->format('d') }}
                                            </p>
                                            <p class="text-xl font-bold text-gray-400 uppercase tracking-[0.3em]">
                                                {{ \Carbon\Carbon::parse($team->defense_date)->format('F Y') }}
                                            </p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10 hover:border-white/30 transition-colors group/item hover:bg-white/10">
                                                <i class="far fa-clock mb-2 text-xl block group-hover/item:scale-110 transition-transform text-[#D4AF37]"></i>
                                                <span class="text-xs font-mono text-white font-bold">
                                                    {{ \Carbon\Carbon::parse($team->defense_date)->format('h:i A') }}
                                                </span>
                                            </div>

                                            <div class="bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10 hover:border-white/30 transition-colors group/item hover:bg-white/10">
                                                <i class="fas fa-map-marker-alt mb-2 text-xl block group-hover/item:scale-110 transition-transform text-[#D4AF37]"></i>
                                                <span class="text-[10px] text-white font-black truncate block uppercase">
                                                    {{ $team->defense_location }}
                                                </span>
                                            </div>
                                        </div>

                                        <button onclick="document.getElementById('defenseForm').classList.toggle('hidden')"
                                            class="mt-6 text-[10px] font-black text-gray-500 hover:text-white uppercase tracking-widest transition-all hover:bg-white/5 py-3 px-4 rounded-xl w-full border border-transparent hover:border-white/10">
                                            <i class="fas fa-edit mr-1"></i> Reschedule Event
                                        </button>
                                    </div>
                                @else
                                    <div class="py-12 border-2 border-dashed border-slate-700 rounded-[2rem] bg-slate-800/30">
                                        <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                                            <i class="fas fa-calendar-plus text-3xl text-slate-600"></i>
                                        </div>
                                        <p class="text-slate-500 text-sm font-black uppercase tracking-widest">Calendar Empty</p>
                                        <p class="text-slate-600 text-[10px] mt-2">Initialize the final defense schedule below</p>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="bg-red-500/20 border border-red-500 text-red-200 p-4 rounded-xl mb-4 text-xs mt-4">
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
                                        <label class="text-[9px] uppercase text-gray-500 font-black mb-2 block ml-2 tracking-widest">Discussion Date</label>
                                        <input type="datetime-local" name="defense_date" required
                                            class="w-full bg-black/40 border border-slate-700 text-white text-xs rounded-xl p-4 focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] outline-none transition-all font-mono hover:bg-black/60 placeholder-gray-600">
                                    </div>
                                    <div class="text-left">
                                        <label class="text-[9px] uppercase text-gray-500 font-black mb-2 block ml-2 tracking-widest">Location / Hall</label>
                                        <input type="text" name="defense_location" placeholder="ex: Main Hall, Room 303"
                                            required
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

                    {{-- ⚖️ 2. Grading Panel (تحكم الدرجات - تحت الـ Defense) --}}
                    <div class="bg-white rounded-[2rem] border-t-8 border-[#D4AF37] shadow-2xl overflow-hidden">
                        
                        {{-- Panel Header --}}
                        <div class="px-6 py-5 border-b border-gray-100 bg-[#D4AF37]/5 flex justify-between items-center">
                            <div>
                                <h3 class="font-black text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-gavel text-[#D4AF37]"></i> Grading Control
                                </h3>
                                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-bold">Set Grades & Scores</p>
                            </div>
                        </div>

                        <form action="{{ route('staff.team.save_all_grades', $team->id) }}" method="POST" class="p-6">
                            @csrf

                            {{-- Project Score --}}
                            @php
                                $currentMax = $team->project_max_score ?? 100;
                                $currentScore = $team->project_score;
                                $percent = ($currentScore && $currentMax > 0) ? round(($currentScore / $currentMax) * 100) : 0;
                                $isPass = $percent >= 50;
                            @endphp

                            <div class="mb-8 p-1 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-50 border border-gray-200">
                                <div class="bg-white rounded-xl p-5 relative overflow-hidden">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Total Project Score</label>
                                    
                                    <div class="flex items-end justify-center gap-1 mb-2">
                                        <input type="number" step="0.5" min="0" name="project_score" value="{{ $currentScore }}" placeholder="0"
                                               class="w-24 text-center text-5xl font-black text-gray-900 border-b-4 border-[#D4AF37] focus:border-black bg-transparent outline-none p-0 transition-colors">
                                        <span class="text-2xl font-bold text-gray-300 mb-2">/</span>
                                        <input type="number" step="1" min="1" name="project_max_score" value="{{ $currentMax }}" 
                                               class="w-16 text-center text-xl font-bold text-gray-400 border-b-2 border-gray-200 focus:border-gray-400 bg-transparent outline-none p-0 transition-colors mb-2">
                                    </div>

                                    @if(!is_null($currentScore))
                                        <div class="text-center">
                                            <span class="inline-block px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest {{ $isPass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $percent }}% — {{ $isPass ? 'PASSED' : 'FAILED' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mb-6">
                                <div class="h-px bg-gray-100 flex-1"></div>
                                <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Individual Grades</span>
                                <div class="h-px bg-gray-100 flex-1"></div>
                            </div>

                            {{-- Individual Members --}}
                            <div class="space-y-4 max-h-[400px] overflow-y-auto custom-scroll pr-1">
                                @foreach($team->members as $member)
                                    @php
                                        $indScore = $member->individual_score;
                                        $indMax = $member->individual_max_score ?? 100;
                                        $indPercent = ($indScore && $indMax > 0) ? round(($indScore / $indMax) * 100) : 0;
                                        $indPass = $indPercent >= 50;
                                    @endphp

                                    <div class="p-3 bg-white rounded-xl border border-gray-100 hover:border-[#D4AF37] hover:shadow-md transition-all group">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <img src="https://ui-avatars.com/api/?name={{ $member->user->name }}&background=random" 
                                                     class="w-8 h-8 rounded-full shadow-sm">
                                                <div class="overflow-hidden">
                                                    <p class="text-xs font-bold text-gray-800 w-28 flex flex-col gap-1 items-start">
                                                        <span class="truncate w-full">{{ $member->user->name }}</span>
                                                        @if($member->auto_group)
                                                            <span class="text-[8px] px-1.5 py-0.5 rounded border {{ $member->auto_group == 'A' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-purple-50 text-purple-600 border-purple-200' }}">
                                                                Grp {{ $member->auto_group }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                    <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">{{ $member->role }}</p>
                                                </div>
                                            </div>
                                            @if(!is_null($indScore))
                                                <span class="text-[9px] font-bold px-2 py-0.5 rounded {{ $indPass ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }}">
                                                    {{ $indPercent }}%
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center bg-gray-50 rounded-lg p-1.5 border border-gray-100 group-hover:bg-white transition-colors">
                                            <input type="number" step="0.5" name="individual_grades[{{ $member->user->id }}]" value="{{ $indScore }}" placeholder="0"
                                                   class="w-full text-center bg-transparent font-bold text-sm text-gray-900 focus:outline-none border-b border-transparent focus:border-[#D4AF37]">
                                            <span class="text-gray-300 text-xs px-1">/</span>
                                            <input type="number" step="1" name="individual_max_scores[{{ $member->user->id }}]" value="{{ $indMax }}"
                                                   class="w-10 text-center bg-transparent font-bold text-xs text-gray-400 focus:outline-none border-b border-transparent focus:border-gray-400">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" 
                                    class="w-full mt-6 bg-[#1a1a1a] hover:bg-black text-[#D4AF37] py-4 rounded-xl font-bold uppercase text-xs tracking-widest shadow-xl shadow-gray-200 hover:shadow-2xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                                <span>Save All Grades</span> <i class="fas fa-save text-lg"></i>
                            </button>
                        </form>
                    </div>

                </div>
            </div> {{-- End Right Column --}}

        </div> {{-- End Main Grid --}}

        {{-- 🚪 Team Members Modal --}}
        <div id="staffTeamMembersModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- الخلفية --}}
                <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"
                    onclick="closeModal('staffTeamMembersModal')"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                {{-- المحتوى --}}
                <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-4 border-[#D4AF37]">
                    
                    {{-- الهيدر --}}
                    <div class="bg-gray-900 p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-10 -mt-10"></div>
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="w-12 h-12 bg-[#D4AF37] rounded-full flex items-center justify-center shadow-lg text-black">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-[#D4AF37]">Team Roster</h3>
                                <p class="text-gray-400 text-xs">{{ $team->name }} • {{ $team->members->count() }} Students</p>
                            </div>
                        </div>
                        <button onclick="closeModal('staffTeamMembersModal')" class="absolute top-4 right-4 text-gray-500 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    {{-- القائمة --}}
                    <div class="p-6 max-h-[60vh] overflow-y-auto bg-gray-50">
                        <div class="space-y-3">
                            @foreach($team->members as $member)
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-[#D4AF37] transition group">
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <img src="https://ui-avatars.com/api/?name={{ $member->user->name }}&background=random" 
                                                class="w-12 h-12 rounded-full border-2 border-white shadow-md">
                                            @if($member->role == 'leader')
                                                <span class="absolute -top-1 -right-1 bg-yellow-400 text-black text-[10px] w-5 h-5 flex items-center justify-center rounded-full shadow-sm border border-white">
                                                    <i class="fas fa-crown"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                                {{ $member->user->name }}
                                                @if($member->auto_group)
                                                <span class="text-[9px] px-2 py-0.5 rounded border uppercase tracking-wider {{ $member->auto_group == 'A' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-purple-50 text-purple-600 border-purple-200' }}">
                                                    Group {{ $member->auto_group }}
                                                </span>
                                                @endif
                                            </h4>
                                            <p class="text-xs text-gray-500 font-mono mt-0.5">
                                                <i class="far fa-envelope text-[#D4AF37] mr-1"></i>{{ $member->user->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $member->role == 'leader' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $member->role }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- زرار إغلاق --}}
                    <div class="bg-white px-6 py-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal('staffTeamMembersModal')" 
                            class="w-full bg-gray-100 text-gray-600 py-3 rounded-xl font-bold hover:bg-gray-200 transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- 📜 Scripts --}}
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
@endsection