@extends('layouts.batu')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-indigo-600 font-bold text-xs uppercase tracking-widest mb-3 transition group">
                <i class="fas fa-arrow-left transition group-hover:-translate-x-1"></i> Back to Quizzes
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 flex items-center gap-4">
                <span class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center shadow-sm">
                    <i class="fas fa-chart-bar"></i>
                </span>
                Results: {{ $quiz->title }}
            </h1>
        </div>
        <div class="flex flex-wrap gap-3 w-full sm:w-auto">
            <button onclick="openParticipationModal()" class="flex-1 sm:flex-none justify-center bg-white border border-gray-200 hover:border-indigo-100 hover:text-indigo-600 text-gray-600 px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-sm transition flex items-center gap-2">
                <i class="fas fa-users-viewfinder"></i> Participation
            </button>
            <a href="{{ route('admin.quizzes.grading', $quiz->id) }}" class="flex-1 sm:flex-none justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-indigo-100 transition flex items-center gap-2">
                <i class="fas fa-check-double"></i> Grading
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl mb-6 font-black text-xs uppercase tracking-widest flex items-center gap-3">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-100 text-rose-600 px-6 py-4 rounded-2xl mb-6 font-black text-xs uppercase tracking-widest flex items-center gap-3">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm whitespace-nowrap">
                <thead class="uppercase tracking-widest border-b border-gray-50 bg-gray-50/50">
                    <tr class="text-[10px] font-black text-gray-400">
                        <th scope="col" class="px-8 py-5">Candidate</th>
                        <th scope="col" class="px-6 py-5 text-center">Status</th>
                        <th scope="col" class="px-6 py-5 text-center">Performance</th>
                        <th scope="col" class="px-6 py-5 text-center">Violations</th>
                        <th scope="col" class="px-6 py-5">Timeline</th>
                        <th scope="col" class="px-8 py-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attempts as $attempt)
                    <tr class="hover:bg-gray-50/50 transition duration-200">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 font-black">
                                    {{ strtoupper(substr($attempt->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-black text-gray-900">{{ $attempt->user->name }}</p>
                                    @if($attempt->attempt_number > 1)
                                        <span class="text-[9px] font-black uppercase tracking-tighter text-indigo-400">Retry Attempt #{{ $attempt->attempt_number }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            @php
                                $statusMap = [
                                    'submitted' => ['emerald', 'Submitted'],
                                    'auto_submitted' => ['indigo', 'Time Up'],
                                    'disqualified' => ['rose', 'DQ'],
                                    'cancelled' => ['gray', 'Cancelled']
                                ];
                                $s = $statusMap[$attempt->status] ?? ['gray', $attempt->status];
                            @endphp
                            <span class="bg-{{ $s[0] }}-50 text-{{ $s[0] }}-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-{{ $s[0] }}-100">{{ $s[1] }}</span>
                            @if($attempt->extra_time_minutes > 0)
                                <div class="mt-2 flex items-center justify-center gap-1 text-[9px] font-black text-amber-500 uppercase tracking-tighter italic">
                                    <i class="fas fa-plus"></i> {{ $attempt->extra_time_minutes }}m Added
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xl font-black {{ $attempt->score >= ($quiz->total_marks/2) ? 'text-emerald-600' : 'text-rose-500' }}">
                                    {{ $attempt->score }}
                                </span>
                                <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">/ {{ $quiz->total_marks }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-black text-xs {{ $attempt->violation_count == 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                {{ $attempt->violation_count }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2">
                                    <i class="far fa-play-circle text-gray-300"></i>
                                    <span class="text-[10px] font-bold text-gray-500">{{ $attempt->started_at ? $attempt->started_at->format('M d, h:i A') : 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="far fa-check-circle text-gray-300"></i>
                                    <span class="text-[10px] font-bold text-gray-500">{{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, h:i A') : 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.quizzes.attempts.review', $attempt->id) }}" class="w-10 h-10 flex items-center justify-center bg-gray-50 text-indigo-500 rounded-xl hover:bg-indigo-600 hover:text-white transition shadow-sm" title="Review Answers">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($attempt->unanswered_questions->count() > 0 && in_array($attempt->status, ['auto_submitted']))
                                    <button type="button"
                                        onclick="openExtraTimeModal({{ $attempt->id }}, {{ $attempt->unanswered_questions->count() }}, '{{ addslashes($attempt->user->name) }}')"
                                        class="w-10 h-10 flex items-center justify-center bg-amber-50 text-amber-500 rounded-xl hover:bg-amber-500 hover:text-white transition shadow-sm" title="Grant Extra Time">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    
                                    <form id="extra-time-form-{{ $attempt->id }}" action="{{ route('admin.quizzes.attempts.extra_time', $attempt->id) }}" method="POST" class="hidden">
                                        @csrf
                                        <input type="hidden" name="extra_minutes" id="extra-minutes-{{ $attempt->id }}">
                                        <input type="hidden" name="notes" id="extra-notes-{{ $attempt->id }}">
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-24 text-center">
                            <div class="flex flex-col items-center gap-4 text-gray-300">
                                <div class="w-20 h-20 rounded-[2rem] bg-gray-50 flex items-center justify-center text-4xl">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <p class="font-black uppercase tracking-widest text-xs">No attempts recorded</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Participation Status Modal --}}
<div id="participation-modal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-5xl max-h-[90vh] rounded-[2.5rem] overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.3)] flex flex-col border border-white">
        {{-- Header --}}
        <div class="p-8 sm:p-10 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50/30">
            <div>
                <h3 class="text-2xl font-black text-gray-900 flex items-center gap-4">
                    <span class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-100">
                        <i class="fas fa-users-viewfinder text-xs"></i>
                    </span>
                    Participation Audit
                </h3>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2 ml-14">Live Audience Breakdown</p>
            </div>
            <button onclick="closeParticipationModal()" class="w-12 h-12 flex items-center justify-center bg-white border border-gray-100 rounded-2xl text-gray-400 hover:text-rose-500 hover:border-rose-100 transition shadow-sm"><i class="fas fa-times"></i></button>
        </div>

        {{-- Filters --}}
        <div class="px-8 py-6 bg-white border-b border-gray-50">
            <div class="flex flex-wrap gap-2">
                <button onclick="showParticipationTab('all')" class="participation-tab-btn active px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition flex items-center gap-2" data-tab="all">
                    All <span class="bg-white/20 px-2 py-0.5 rounded ml-1">{{ count($participation['completed']) + count($participation['in_progress']) + count($participation['not_started']) }}</span>
                </button>
                <button onclick="showParticipationTab('completed')" class="participation-tab-btn px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition text-emerald-600 bg-emerald-50 border border-emerald-100 flex items-center gap-2" data-tab="completed">
                    Completed <span class="bg-emerald-600/10 px-2 py-0.5 rounded ml-1">{{ count($participation['completed']) }}</span>
                </button>
                <button onclick="showParticipationTab('in_progress')" class="participation-tab-btn px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition text-indigo-600 bg-indigo-50 border border-indigo-100 flex items-center gap-2" data-tab="in_progress">
                    In Progress <span class="bg-indigo-600/10 px-2 py-0.5 rounded ml-1">{{ count($participation['in_progress']) }}</span>
                </button>
                <button onclick="showParticipationTab('not_started')" class="participation-tab-btn px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition text-gray-400 bg-gray-50 border border-gray-100 flex items-center gap-2" data-tab="not_started">
                    Pending <span class="bg-gray-200 px-2 py-0.5 rounded ml-1">{{ count($participation['not_started']) }}</span>
                </button>
            </div>
        </div>

        {{-- Scrollable Grid --}}
        <div class="flex-1 overflow-y-auto px-8 py-10 bg-gray-50/20">
            <div id="participation-items-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- COMPLETED --}}
                @foreach($participation['completed'] as $item)
                <div class="participation-item p-6 bg-white rounded-3xl border border-emerald-100 shadow-sm flex items-start justify-between group hover:border-emerald-300 transition duration-300" data-status="completed">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-xl shadow-inner group-hover:bg-emerald-100 transition">
                            {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="font-black text-gray-800 text-sm truncate w-full">{{ $item['user']->name }}</p>
                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-tight mt-1">{{ $item['role'] }}</p>
                            <div class="mt-4 flex flex-col gap-1">
                                <span class="text-[9px] font-black text-emerald-500 uppercase flex items-center gap-1.5"><i class="fas fa-check-circle"></i> Submitted</span>
                                <span class="text-[9px] font-bold text-gray-400">{{ $item['attempt']->submitted_at->format('M d, h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- IN PROGRESS --}}
                @foreach($participation['in_progress'] as $item)
                <div class="participation-item p-6 bg-white rounded-3xl border border-indigo-100 shadow-sm flex items-start justify-between group hover:border-indigo-300 transition duration-300" data-status="in_progress">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xl shadow-inner group-hover:bg-indigo-100 transition animate-pulse">
                            {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="font-black text-gray-800 text-sm truncate w-full">{{ $item['user']->name }}</p>
                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-tight mt-1">{{ $item['role'] }}</p>
                            <div class="mt-4 flex flex-col gap-1">
                                <span class="text-[9px] font-black text-indigo-500 uppercase flex items-center gap-1.5"><i class="fas fa-satellite-dish"></i> Live Active</span>
                                <span class="text-[9px] font-bold text-gray-400">Step {{ $item['attempt']->current_step }} in progress</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- NOT STARTED --}}
                @foreach($participation['not_started'] as $item)
                <div class="participation-item p-6 bg-white/60 rounded-3xl border border-gray-100 shadow-sm flex items-start justify-between group hover:bg-white hover:border-gray-300 transition duration-300" data-status="not_started">
                    <div class="flex items-start gap-4 opacity-50">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center font-black text-xl shadow-inner">
                            {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="font-black text-gray-800 text-sm truncate w-full">{{ $item['user']->name }}</p>
                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-tight mt-1">{{ $item['role'] }}</p>
                            <div class="mt-4">
                                <span class="text-[9px] font-black text-gray-300 uppercase italic">Pending Entry</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="p-8 sm:p-10 border-t border-gray-50 bg-gray-50/30 flex justify-end">
            <button onclick="closeParticipationModal()" class="w-full sm:w-auto px-10 py-4 bg-gray-900 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-2xl hover:bg-black transition transform active:scale-95">Dismiss Audit</button>
        </div>
    </div>
</div>

{{-- Extra Time Modal --}}
<div id="extra-time-modal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center">
    <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-black text-gray-900"><i class="fas fa-clock text-yellow-500 mr-2"></i> Grant Extra Time</h3>
            <button onclick="closeExtraTimeModal()" class="text-gray-400 hover:text-black text-xl"><i class="fas fa-times"></i></button>
        </div>

        <p class="text-sm text-gray-600 mb-1">Member: <strong id="modal-member-name" class="text-gray-900"></strong></p>
        <p class="text-sm text-gray-600 mb-4">Unanswered questions: <strong id="modal-unanswered-count" class="text-amber-600"></strong></p>

        <label class="block text-sm font-bold text-gray-700 mb-2">Extra time duration</label>
        <div class="flex gap-2 mb-4">
            <button type="button" onclick="setMinutes(5)" class="flex-1 bg-gray-100 hover:bg-yellow-100 text-gray-700 font-bold py-2 rounded-lg text-sm transition">5 min</button>
            <button type="button" onclick="setMinutes(10)" class="flex-1 bg-gray-100 hover:bg-yellow-100 text-gray-700 font-bold py-2 rounded-lg text-sm transition">10 min</button>
            <button type="button" onclick="setMinutes(15)" class="flex-1 bg-gray-100 hover:bg-yellow-100 text-gray-700 font-bold py-2 rounded-lg text-sm transition">15 min</button>
            <button type="button" onclick="setMinutes(30)" class="flex-1 bg-gray-100 hover:bg-yellow-100 text-gray-700 font-bold py-2 rounded-lg text-sm transition">30 min</button>
        </div>

        <label class="block text-sm font-bold text-gray-700 mb-1">Custom (minutes)</label>
        <input type="number" id="modal-minutes-input" min="1" max="120" value="10" class="w-full rounded-xl border-gray-300 focus:border-yellow-500 focus:ring-yellow-400 mb-4 text-sm">

        <label class="block text-sm font-bold text-gray-700 mb-1">Admin Note (Optional)</label>
        <textarea id="modal-notes-input" rows="2" class="w-full rounded-xl border-gray-300 focus:border-yellow-500 focus:ring-yellow-400 mb-5 text-sm" placeholder="e.g. Approved due to network issue..."></textarea>

        <div class="flex gap-3">
            <button onclick="closeExtraTimeModal()" class="flex-1 bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold py-3 rounded-xl transition">Cancel</button>
            <button onclick="submitExtraTime()" class="flex-1 bg-yellow-500 text-white hover:bg-yellow-600 font-bold py-3 rounded-xl shadow-md transition">
                <i class="fas fa-check mr-1"></i> Grant Extra Time
            </button>
        </div>
    </div>
</div>

<style>
    .participation-tab-btn.active {
        background: #1e293b;
        color: white;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
</style>

<script>
    let activeAttemptId = null;

    function openExtraTimeModal(attemptId, unansweredCount, memberName) {
        activeAttemptId = attemptId;
        document.getElementById('modal-member-name').textContent = memberName;
        document.getElementById('modal-unanswered-count').textContent = unansweredCount + ' question(s)';
        document.getElementById('modal-minutes-input').value = 10;
        document.getElementById('modal-notes-input').value = '';
        const modal = document.getElementById('extra-time-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeExtraTimeModal() {
        activeAttemptId = null;
        const modal = document.getElementById('extra-time-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function setMinutes(n) {
        document.getElementById('modal-minutes-input').value = n;
    }

    function submitExtraTime() {
        if (!activeAttemptId) return;
        const minutes = document.getElementById('modal-minutes-input').value;
        const notes = document.getElementById('modal-notes-input').value;

        document.getElementById('extra-minutes-' + activeAttemptId).value = minutes;
        document.getElementById('extra-notes-' + activeAttemptId).value = notes;
        document.getElementById('extra-time-form-' + activeAttemptId).submit();
    }

    function openParticipationModal() {
        const modal = document.getElementById('participation-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeParticipationModal() {
        const modal = document.getElementById('participation-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function showParticipationTab(tab) {
        // Update Buttons
        document.querySelectorAll('.participation-tab-btn').forEach(btn => {
            btn.classList.remove('active');
            if(btn.dataset.tab === tab) btn.classList.add('active');
        });

        // Filter Items
        document.querySelectorAll('.participation-item').forEach(item => {
            if(tab === 'all') {
                item.classList.remove('hidden');
            } else {
                if(item.dataset.status === tab) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            }
        });
    }
</script>
@endsection
