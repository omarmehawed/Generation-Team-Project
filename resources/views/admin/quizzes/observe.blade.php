@extends('layouts.batu')

@section('content')
<div class="max-w-7xl mx-auto py-8 text-gray-800 dark:text-gray-200" x-data="liveObserver('{{ $attempt->id }}')" x-init="startSync()">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-5">
            <a href="{{ route('admin.quizzes.live', $attempt->quiz_id) }}" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-gray-400 hover:text-indigo-600 hover:shadow-lg transition shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100 flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                        <i class="fas fa-eye"></i>
                    </span>
                    Observing: {{ $attempt->user->name }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 font-bold text-sm mt-1">Quiz: {{ $attempt->quiz->title }} • Attempt #{{ $attempt->attempt_number }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3 px-6 py-4 bg-white dark:bg-gray-800 rounded-[1.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
            <template x-if="isSyncing">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest">Live Syncing</span>
                </div>
            </template>
            <template x-if="!isSyncing">
                <div class="flex items-center gap-3">
                    <span class="h-3 w-3 rounded-full bg-gray-300"></span>
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Idle</span>
                </div>
            </template>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <!-- Time -->
        <div class="p-6 rounded-[2.5rem] bg-indigo-600 text-white shadow-2xl shadow-indigo-200 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-hourglass-start"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Remaining</span>
            </div>
            <p class="text-3xl font-black font-mono leading-none mb-1" x-text="formatTime(detail.time_remaining)"></p>
            <p class="text-[10px] font-bold opacity-80 uppercase tracking-tighter">Minutes Left</p>
        </div>

        <!-- Violations -->
        <div class="p-6 rounded-[2.5rem] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-100/50 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                    <i class="fas fa-shield-virus"></i>
                </div>
                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Violations</span>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-gray-100 leading-none mb-1" x-text="detail.violation_count">0</p>
            <p class="text-[10px] font-bold text-red-500 uppercase tracking-tighter" x-show="detail.violation_count > 0">Anti-Cheat Flags</p>
            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-tighter" x-show="detail.violation_count === 0">Secure Session</p>
        </div>

        <!-- Progress -->
        <div class="p-6 rounded-[2.5rem] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-100/50 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                    <i class="fas fa-tasks"></i>
                </div>
                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Progress</span>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-gray-100 leading-none mb-1" x-text="detail.current_step + '/' + detail.last_page"></p>
            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tighter">Question Groups</p>
        </div>

        <!-- Latest activity -->
        <div class="p-6 rounded-[2.5rem] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-100/50 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                    <i class="fas fa-history"></i>
                </div>
                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Activity</span>
            </div>
            <p class="text-xl font-black text-gray-900 dark:text-gray-100 leading-none mb-2 mt-1 truncate" x-text="detail.last_activity"></p>
            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-tighter">Last Response Sync</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-700 shadow-2xl p-8 md:p-12">
        <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-50">
            <div class="w-2 h-8 bg-indigo-600 rounded-full"></div>
            <h2 class="text-xl font-black text-gray-900 dark:text-gray-100 uppercase tracking-widest">Active Question Group</h2>
        </div>

        <!-- Questions List -->
        <div x-show="loading" class="flex flex-col items-center justify-center py-32 text-center">
            <div class="relative w-20 h-20 mb-6">
                <div class="absolute inset-0 border-4 border-indigo-100 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <p class="text-gray-900 dark:text-gray-100 font-black uppercase tracking-[0.3em] text-xs">Capturing Live Stream...</p>
        </div>

        <div x-show="!loading" class="space-y-12">
            <template x-for="(q, idx) in questions" :key="q.id">
                <div class="bg-gray-50/50 rounded-[3rem] border border-gray-100 dark:border-gray-700 p-8 md:p-10 hover:bg-white hover:shadow-2xl transition duration-500 group">
                    <div class="flex flex-wrap justify-between items-start gap-4 mb-8">
                        <div class="inline-flex items-center gap-3 px-5 py-2.5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <span class="font-black text-indigo-600 text-sm uppercase tracking-widest" x-text="'Question ' + (((detail.current_step-1)*10) + idx + 1)"></span>
                            <span class="w-1.5 h-1.5 bg-gray-200 rounded-full"></span>
                            <span class="text-xs font-bold text-gray-400" x-text="q.marks + ' Points Value'"></span>
                        </div>
                        <div x-show="answers[q.id]" class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest flex items-center gap-2 shadow-sm">
                            <i class="fas fa-check-double scale-110"></i> Draft Submitted
                        </div>
                        <div x-show="!answers[q.id]" class="bg-amber-100 text-amber-700 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest flex items-center gap-2 shadow-sm">
                            <i class="fas fa-spinner fa-spin"></i> Pending Response
                        </div>
                    </div>

                    <div class="mb-10">
                        <p class="font-black text-gray-800 dark:text-gray-200 text-2xl leading-relaxed" x-text="q.question_text"></p>
                    </div>

                    <!-- MCQ Choices -->
                    <template x-if="q.question_type === 'mcq'">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="opt in q.options" :key="opt.id">
                                <div class="p-6 rounded-3xl border-2 font-bold flex items-center gap-4 transition-all duration-300"
                                     :class="answers[q.id]?.selected_option_id == opt.id ?'bg-indigo-600 text-white border-indigo-600 shadow-xl shadow-indigo-200 scale-[1.02]' : 'bg-white text-gray-400 border-gray-100 group-hover:border-gray-200'">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2" 
                                         :class="answers[q.id]?.selected_option_id == opt.id ?'border-indigo-400 bg-indigo-500' : 'border-gray-100'">
                                        <div class="w-2.5 h-2.5 rounded-full bg-white dark:bg-gray-800" x-show="answers[q.id]?.selected_option_id == opt.id"></div>
                                    </div>
                                    <span class="text-base" x-text="opt.option_text"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Written Text -->
                    <template x-if="q.question_type === 'written'">
                        <div class="p-8 rounded-[2rem] border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 group-hover:border-indigo-100 transition shadow-inner">
                            <div class="flex items-center gap-2 mb-4 text-xs font-black uppercase tracking-widest text-indigo-400">
                                <i class="fas fa-pen-nib"></i> Candidate's Draft Response
                            </div>
                            <p class="text-bases font-bold text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed min-h-[80px]" x-text="answers[q.id]?.text_answer || 'No input recorded yet. Awaiting candidate interaction...'"></p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function liveObserver(attemptId) {
    return {
        id: attemptId,
        loading: true,
        isSyncing: false,
        detail: {},
        questions: [],
        answers: {},
        syncInterval: null,

        async fetchState() {
            this.isSyncing = true;
            try {
                const response = await fetch('{{ route('admin.quizzes.attempts.details', 999999) }}'.replace('999999', this.id));
                const data = await response.json();
                
                this.detail = data.attempt;
                this.questions = data.questions;
                this.answers = data.answers;
            } catch (err) {
                console.error("Observer Sync Failed", err);
            } finally {
                this.loading = false;
                this.isSyncing = false;
            }
        },

        startSync() {
            this.fetchState();
            // sync every 8 seconds for a "more live" feel on the dedicated page
            this.syncInterval = setInterval(() => {
                this.fetchState();
            }, 8000);
        },

        formatTime(seconds) {
            if (seconds <= 0) return '00:00';
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}:${s < 10 ? '0'+s : s}`;
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
