@extends('layouts.batu')

@section('content')
<div class="max-w-7xl mx-auto py-8 text-gray-800">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="text-gray-500 hover:text-gray-900 font-bold text-sm mb-2 inline-flex items-center gap-1 transition">
                <i class="fas fa-arrow-left"></i> Back to Quizzes
            </a>
            <h1 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                <span class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center shadow-sm">
                    <i class="fas fa-satellite-dish animate-pulse"></i>
                </span>
                Live Monitor: {{ $quiz->title }}
            </h1>
        </div>
        <div class="flex items-center gap-3 px-5 py-3 bg-white rounded-2xl shadow-sm border border-gray-100">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-sm font-black text-gray-600 tracking-tight">Live Updates Active</span>
        </div>
    </div>

    <!-- Live Card -->
    <div x-data="liveMonitor()" x-init="startPolling()" class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100 flex flex-col">
        <div class="p-6 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-600 px-4 py-2 rounded-2xl shadow-lg shadow-indigo-200">
                    <p class="text-white text-xs font-black uppercase tracking-widest opacity-80 mb-0.5">Active</p>
                    <p class="text-white text-2xl font-black leading-none" x-text="attempts.length">0</p>
                </div>
                <h2 class="text-xl font-black text-gray-800">Candidates Online</h2>
            </div>
            <button @click="fetchData()" class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-black text-gray-700 hover:bg-gray-50 transition shadow-sm active:scale-95">
                <i class="fas fa-sync-alt" :class="{'fa-spin': isFetching}"></i> Refresh Now
            </button>
        </div>

        <div class="overflow-x-auto overflow-y-hidden">
            <table class="min-w-full text-left whitespace-nowrap">
                <thead class="bg-white border-b border-gray-100">
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                        <th class="px-8 py-6">Member Name</th>
                        <th class="px-6 py-6 text-center">Status</th>
                        <th class="px-6 py-6 text-center">Time Left</th>
                        <th class="px-6 py-6 text-center">Step</th>
                        <th class="px-6 py-6 text-center">Violations</th>
                        <th class="px-6 py-6 text-center">Last Active</th>
                        <th class="px-6 py-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="attempt in attempts" :key="attempt.id">
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-colors">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="font-bold text-gray-800" x-text="attempt.user_name"></span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span x-show="attempt.status === 'in_progress'" class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-xs font-black animate-pulse">
                                    In Progress
                                </span>
                                <span x-show="attempt.status === 'pending'" class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-xs font-black">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span x-show="attempt.time_remaining > 0" :class="attempt.time_remaining < 300 ? 'text-red-500' : 'text-gray-900'" class="font-black font-mono text-lg" x-text="formatTime(attempt.time_remaining)"></span>
                                <span x-show="attempt.time_remaining <= 0" class="text-red-500 font-black uppercase text-xs tracking-widest bg-red-50 px-2 py-1 rounded-lg">Suspended</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-xl font-black text-sm" x-text="attempt.current_step"></span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 px-3 py-1.5 rounded-xl border border-red-100" x-show="attempt.violation_count > 0">
                                    <i class="fas fa-shield-virus text-xs"></i>
                                    <span class="font-black text-sm leading-none" x-text="attempt.violation_count"></span>
                                </div>
                                <span x-show="attempt.violation_count === 0" class="text-emerald-500 font-black text-sm">Safe</span>
                            </td>
                            <td class="px-6 py-6 text-center text-[10px] text-gray-400 font-black uppercase tracking-wider" x-text="attempt.last_activity ?? 'Unknown'"></td>
                            <td class="px-6 py-6">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="viewDetail(attempt.id)" class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-indigo-200 transition transform active:scale-95">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <form :action="'{{ route('admin.quizzes.attempts.cancel', 999999) }}'.replace('999999', attempt.id)" method="POST" onsubmit="return confirm('Force end this attempt?')">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-2xl text-xs font-black shadow-lg shadow-red-200 transition transform active:scale-95">
                                            <i class="fas fa-ban"></i> Cancel Exam
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="attempts.length === 0">
                        <td colspan="7" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center gap-4 text-gray-300">
                                <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center text-3xl">
                                    <i class="fas fa-user-slash"></i>
                                </div>
                                <p class="font-black uppercase tracking-widest text-sm">No Active Candidates</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Attempt Detail Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" @click="showModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col relative z-20 transition-all transform" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex justify-between items-start">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-3xl bg-white border border-gray-100 shadow-xl shadow-gray-100 flex items-center justify-center text-indigo-600 text-2xl">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 leading-tight mb-1" x-text="detail.user_name">Candidate</h3>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest">
                                <i class="fas fa-step-forward"></i> Step <span x-text="detail.current_step"></span> / <span x-text="detail.last_page"></span>
                            </div>
                            <span class="text-xs font-bold text-gray-400" x-text="'Attempt ID: #' + detail.id"></span>
                        </div>
                    </div>
                </div>
                <button @click="showModal = false" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-100 text-gray-400 hover:text-red-500 hover:shadow-lg transition shadow-sm">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 custom-scroll bg-white">
                <div x-show="loadingDetail" class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="relative w-16 h-16 mb-6">
                        <div class="absolute inset-0 border-4 border-indigo-100 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <p class="text-gray-900 font-black uppercase tracking-[0.2em] text-xs">Capturing Live State...</p>
                </div>

                <div x-show="!loadingDetail" class="space-y-10">
                    <!-- Status Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 rounded-[2rem] bg-indigo-50/50 border border-indigo-100 flex items-center gap-5 transform hover:scale-[1.02] transition">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-xl shadow-lg shadow-indigo-100">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-indigo-400 font-black uppercase tracking-wider">Remaining Time</p>
                                <p class="font-black text-indigo-900 text-xl leading-none" x-text="formatTime(detail.time_remaining)"></p>
                            </div>
                        </div>
                        <div class="p-6 rounded-[2rem] bg-red-50 text-red-900 border border-red-100 flex items-center gap-5 transform hover:scale-[1.02] transition">
                            <div class="w-14 h-14 rounded-2xl bg-red-500 flex items-center justify-center text-white text-xl shadow-lg shadow-red-100">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-red-400 font-black uppercase tracking-wider">Violations</p>
                                <p class="font-black text-red-900 text-xl leading-none" x-text="detail.violation_count"></p>
                            </div>
                        </div>
                        <div class="p-6 rounded-[2rem] bg-emerald-50 text-emerald-900 border border-emerald-100 flex items-center gap-5 transform hover:scale-[1.02] transition">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-500 flex items-center justify-center text-white text-xl shadow-lg shadow-emerald-100">
                                <i class="fas fa-running"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-emerald-400 font-black uppercase tracking-wider">Last Sync</p>
                                <p class="font-black text-emerald-900 text-sm leading-none" x-text="detail.last_activity"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Layout for questions -->
                    <div class="pt-6">
                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-[0.3em] flex items-center gap-3 mb-8">
                            <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
                            Visible Questions & Current Drafts
                        </h4>
                        
                        <div class="grid grid-cols-1 gap-8">
                            <template x-for="(q, idx) in detailQuestions" :key="q.id">
                                <div class="bg-gray-50/50 rounded-[2.5rem] border border-gray-100 p-8 hover:bg-white hover:shadow-xl transition group">
                                    <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-2xl shadow-sm border border-gray-100">
                                            <span class="font-black text-indigo-600 text-xs uppercase tracking-widest" x-text="'Question ' + (((detail.current_step-1)*10) + idx + 1)"></span>
                                            <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                            <span class="text-[10px] font-bold text-gray-400 lowercase tracking-tight" x-text="q.marks + ' points'"></span>
                                        </div>
                                        <div x-show="detailAnswers[q.id]" class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5">
                                            <i class="fas fa-check-double text-[8px]"></i> Answer Drafted
                                        </div>
                                    </div>

                                    <div class="mb-8">
                                        <p class="font-black text-gray-800 text-lg leading-relaxed mb-1" x-text="q.question_text"></p>
                                    </div>

                                    <!-- MCQ options -->
                                    <div x-show="q.question_type === 'mcq'" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <template x-for="opt in q.options" :key="opt.id">
                                            <div class="p-5 rounded-2xl border-2 font-bold flex items-center gap-3 transition-all"
                                                 :class="detailAnswers[q.id]?.selected_option_id == opt.id ? 'bg-indigo-600 text-white border-indigo-600 shadow-xl shadow-indigo-100' : 'bg-white text-gray-400 border-gray-100 group-hover:border-gray-200'">
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center border-2" :class="detailAnswers[q.id]?.selected_option_id == opt.id ? 'border-indigo-400 bg-indigo-500' : 'border-gray-100'">
                                                    <div class="w-2 h-2 rounded-full bg-white" x-show="detailAnswers[q.id]?.selected_option_id == opt.id"></div>
                                                </div>
                                                <span class="text-sm" x-text="opt.option_text"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Written logic -->
                                    <div x-show="q.question_type === 'written'">
                                        <div class="p-6 rounded-3xl border-2 border-gray-100 bg-white group-hover:border-indigo-100 transition shadow-inner">
                                            <div class="flex items-center gap-2 mb-3 text-[10px] font-black uppercase tracking-widest text-indigo-400">
                                                <i class="fas fa-pen-nib"></i> Draft Response
                                            </div>
                                            <p class="text-sm font-bold text-gray-700 whitespace-pre-wrap leading-relaxed min-h-[50px]" x-text="detailAnswers[q.id]?.text_answer || 'Field remains empty...'"></p>
                                        </div>
                                    </div>

                                    <div x-show="!detailAnswers[q.id]" class="mt-6 p-4 rounded-2xl bg-amber-50 border border-amber-100 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xs shadow-lg shadow-amber-200">
                                            <i class="fas fa-ghost"></i>
                                        </div>
                                        <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest">Awaiting interaction from candidate</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-4">
                <button @click="showModal = false" class="px-10 py-4 bg-white border border-gray-200 rounded-[1.5rem] font-black text-gray-700 hover:bg-gray-100 transition shadow-sm active:scale-95">
                    Close Monitor
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function liveMonitor() {
    return {
        attempts: [],
        isFetching: false,
        intervalId: null,
        
        // Modal State
        showModal: false,
        loadingDetail: false,
        detail: {},
        detailQuestions: [],
        detailAnswers: {},
        
        async fetchData() {
            this.isFetching = true;
            try {
                const response = await fetch('{{ route('admin.quizzes.live.data', $quiz->id) }}');
                const data = await response.json();
                this.attempts = data.attempts;
            } catch (err) {
                console.error("Live monitor fetch error", err);
            }
            this.isFetching = false;
        },

        async viewDetail(attemptId) {
            this.showModal = true;
            this.loadingDetail = true;
            
            try {
                const response = await fetch('{{ route('admin.quizzes.attempts.details', 999) }}'.replace('999', attemptId));
                const data = await response.json();
                
                this.detail = data.attempt;
                this.detailQuestions = data.questions;
                this.detailAnswers = data.answers;
            } catch (err) {
                console.error("Attempt detail fetch error", err);
                this.showModal = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Sync Failed',
                    text: 'Unable to capture live attempt state. Please check connection.',
                    confirmButtonText: 'Understood',
                    confirmButtonColor: '#4f46e5'
                });
            } finally {
                this.loadingDetail = false;
            }
        },

        formatTime(seconds) {
            if (seconds <= 0) return '00:00';
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}:${s < 10 ? '0'+s : s}`;
        },

        startPolling() {
            this.fetchData();
            // Poll every 10 seconds
            this.intervalId = setInterval(() => {
                this.fetchData();
                
                // If modal is open, refresh detail as well
                if (this.showModal && !this.loadingDetail) {
                    this.refreshActiveDetail();
                }
            }, 10000);
        },

        async refreshActiveDetail() {
            if (!this.detail.id) return;
            try {
                const response = await fetch('{{ route('admin.quizzes.attempts.details', 999) }}'.replace('999', this.detail.id));
                const data = await response.json();
                this.detail = data.attempt;
                this.detailQuestions = data.questions;
                this.detailAnswers = data.answers;
            } catch (err) {
                console.warn("Silent refresh failed", err);
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
.custom-scroll::-webkit-scrollbar {
    width: 6px;
}
.custom-scroll::-webkit-scrollbar-track {
    background: #f8fafc;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>
@endsection
