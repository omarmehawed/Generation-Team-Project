@extends('layouts.staff')

@section('content')
    {{-- ✨ ستايلات الفخامة --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');

        .premium-bg {
            background: var(--bg-main);
            font-family: 'Cairo', sans-serif;
            color: var(--text-main);
        }

        .glass-card {
            background: var(--bg-panel);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            border-color: var(--primary);
        }

        .animate-fade-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        /* Text Overrides for Dark Mode Compatibility */
        .text-slate-800 {
            color: var(--text-main) !important;
        }

        .text-slate-500 {
            color: var(--text-muted) !important;
        }

        .text-slate-400 {
            color: var(--text-muted) !important;
        }

        .bg-slate-50 {
            background-color: var(--bg-main) !important;
        }

        .border-slate-100 {
            border-color: var(--border) !important;
        }
    </style>

    <div class="premium-bg min-h-screen p-6 md:p-10 animate-fade-up">

        {{-- 👑 Header --}}
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-800">
                    Dashboard <span class="text-[#D4AF37]">.</span>
                </h1>
                <p class="text-slate-500 font-bold mt-2">
                    Welcome back, <span class="text-[#175c53]">Dr. {{ Auth::user()->name }}</span>
                </p>
            </div>
            <div class="bg-white px-5 py-2 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-2">
                <i class="far fa-calendar-alt text-[#D4AF37]"></i>
                <span
                    class="text-xs font-black text-slate-600 uppercase">{{ \Carbon\Carbon::now()->format('l, d M Y') }}</span>
            </div>
        </div>

        {{-- 📊 1. Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

            {{-- Total Teams --}}
            <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-[#175c53]/10 rounded-full group-hover:scale-150 transition-transform duration-500">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 bg-[#175c53] text-white rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-[#175c53]/30">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Teams</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $stats['total_teams'] }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Graduation & Subjects</p>
                </div>
            </div>

            {{-- Pending Proposals --}}
            <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-yellow-100/50 rounded-full group-hover:scale-150 transition-transform duration-500">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 bg-[#D4AF37] text-white rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-yellow-500/30">
                        <i class="fas fa-file-signature text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">New Proposals</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $stats['pending_proposals'] }}</h3>
                    <a href="{{ route('staff.proposals') }}"
                        class="text-[10px] font-bold text-[#D4AF37] mt-1 hover:underline">Review Now &rarr;</a>
                </div>
            </div>

            {{-- Active Projects --}}
            <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-blue-100/50 rounded-full group-hover:scale-150 transition-transform duration-500">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-blue-500/30">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Projects</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $stats['active_projects'] }}</h3>
                    <p class="text-[10px] text-blue-500 mt-1 font-bold">In Progress</p>
                </div>
            </div>

            {{-- Completed --}}
            <div class="glass-card p-6 rounded-[2rem] relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-green-100/50 rounded-full group-hover:scale-150 transition-transform duration-500">
                </div>
                <div class="relative z-10">
                    <div
                        class="w-12 h-12 bg-green-600 text-white rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-green-500/30">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Completed</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $stats['completed_projects'] }}</h3>
                    <p class="text-[10px] text-green-600 mt-1 font-bold">Archived</p>
                </div>
            </div>
        </div>

        {{-- ⚔️ 2. Main Content Split --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            {{-- 🟢 Left Column: Reports & Meetings (2/3 width) --}}
            <div class="xl:col-span-2 space-y-8">

                {{-- Recent Reports --}}
                <div class="glass-card rounded-[2.5rem] overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-slate-800 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center"><i
                                    class="fas fa-clipboard-list"></i></span>
                            Pending Reports
                        </h3>
                        <span
                            class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full">{{ $recent_reports->count() }}
                            New</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-[10px] uppercase text-slate-400 font-black tracking-wider">
                                <tr>
                                    <th class="px-8 py-4">Team Info</th>
                                    <th class="px-8 py-4">Context</th>
                                    <th class="px-8 py-4">Report Details</th>
                                    <th class="px-8 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recent_reports as $report)
                                    <tr class="hover:bg-indigo-50/10 transition group">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs uppercase group-hover:bg-[#175c53] group-hover:text-white transition">
                                                    {{ substr($report->team->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800 text-sm">{{ $report->team->name }}
                                                    </p>
                                                    <p class="text-[10px] text-slate-400">Week #{{ $report->week_number }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            @if ($report->team->project && $report->team->project->course)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-100">
                                                    <i class="fas fa-book"></i> {{ $report->team->project->course->code }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100">
                                                    <i class="fas fa-graduation-cap"></i> Graduation
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-5">
                                            <p class="text-xs text-slate-600 italic line-clamp-1 w-48">
                                                "{{ $report->achievements }}"</p>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <a href="{{ route('staff.team.view', $report->team_id) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-slate-200 text-slate-400 hover:bg-[#175c53] hover:text-white hover:border-[#175c53] transition">
                                                <i class="fas fa-arrow-right text-xs"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-slate-400 text-xs font-bold uppercase">No
                                            reports
                                            needing review</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Previous Meetings --}}
                <div class="glass-card rounded-[2.5rem] overflow-hidden border border-slate-200">
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center"><i
                                class="fas fa-history"></i></span>
                        <h3 class="font-bold text-slate-800">Recent Meeting Logs</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($previousMeetings as $meeting)
                            <div
                                class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-slate-300 transition">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex flex-col items-center justify-center w-12 h-12 bg-white rounded-xl shadow-sm">
                                        <span
                                            class="text-[9px] font-black text-slate-400 uppercase">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M') }}</span>
                                        <span
                                            class="text-lg font-black text-slate-800">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d') }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm">{{ $meeting->topic }}</h4>
                                        <p class="text-xs text-slate-500">{{ $meeting->team->name }}</p>
                                    </div>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-1 rounded-md border border-green-100"><i
                                        class="fas fa-check"></i> Done</span>
                            </div>
                        @empty
                            <p class="text-center text-xs text-slate-400 py-4">No meeting history.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- 🔵 Right Column: Events & Urgent (1/3 width) --}}
            <div class="space-y-8">

                {{-- Upcoming Defenses (Graduation Only) --}}
                <div
                    class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-40 h-40 bg-[#D4AF37] opacity-10 rounded-full blur-3xl -mr-10 -mt-10">
                    </div>
                    <h3 class="text-lg font-black mb-6 flex items-center gap-3 relative z-10">
                        <i class="fas fa-calendar-star text-[#D4AF37]"></i> Upcoming Defenses
                    </h3>
                    <div class="space-y-6 relative z-10">
                        @forelse($upcoming_defenses as $team)
                            <div class="relative pl-6 border-l-2 border-white/10 hover:border-[#D4AF37] transition group">
                                <span
                                    class="absolute -left-[5px] top-0 w-2.5 h-2.5 rounded-full bg-slate-700 group-hover:bg-[#D4AF37] transition"></span>
                                <h4 class="font-bold text-sm">{{ $team->name }}</h4>
                                <p class="text-xs text-slate-400 mt-1 mb-2">{{ $team->proposal_title }}</p>
                                <div class="flex items-center gap-3 text-xs font-mono text-[#D4AF37]">
                                    <span><i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($team->defense_date)->format('d M, H:i') }}</span>
                                    <span><i class="fas fa-map-marker-alt"></i> {{ $team->defense_location }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 opacity-50">
                                <i class="fas fa-calendar-check text-4xl mb-3 block"></i>
                                <p class="text-sm">No defenses scheduled.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Upcoming Meetings (Mixed) --}}
                <div class="glass-card rounded-[2.5rem] p-6">
                    <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-handshake text-blue-600"></i> Next Meetings
                    </h3>
                    <div class="space-y-4">
                        @forelse($upcomingMeetings as $meeting)
                            <div
                                class="flex flex-col p-4 bg-blue-50/50 rounded-2xl border border-blue-100 hover:bg-white hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="text-[10px] font-black uppercase text-blue-400 tracking-wider">{{ \Carbon\Carbon::parse($meeting->meeting_date)->diffForHumans() }}</span>
                                    @if ($meeting->status == 'pending')
                                        <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    @endif
                                </div>
                                <h4 class="font-bold text-slate-800 text-sm mb-1">{{ $meeting->topic }}</h4>
                                <div class="flex items-center justify-between mt-2">
                                    <span
                                        class="text-xs text-slate-500 font-medium bg-white px-2 py-1 rounded-md border border-slate-200">{{ $meeting->team->name }}</span>
                                    @if ($meeting->status == 'pending')
                                        <a href="{{ route('staff.team.view', $meeting->team_id) }}#meetings-section"
                                            class="text-[10px] font-bold text-white bg-blue-600 px-3 py-1 rounded-full hover:bg-blue-700">Accept</a>
                                    @else
                                        <span class="text-[10px] font-bold text-green-600"><i class="fas fa-check"></i>
                                            Active</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-xs text-slate-400 py-4">No upcoming meetings.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection