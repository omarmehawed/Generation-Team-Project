@extends('layouts.batu')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="mb-4 flex justify-between items-end">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="text-gray-500 hover:text-black font-bold text-sm mb-2 inline-block"><i class="fas fa-arrow-left"></i> Back to Quizzes</a>
            <h1 class="text-3xl font-black text-gray-800"><i class="fas fa-chart-bar text-blue-500 mr-2"></i> Results: {{ $quiz->title }}</h1>
        </div>
        <div class="flex gap-2">
            <button onclick="openParticipationModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl font-bold text-sm shadow-sm transition">
                <i class="fas fa-users-viewfinder mr-1"></i> Participation Status
            </button>
            <a href="{{ route('admin.quizzes.grading', $quiz->id) }}" class="btn-royal-gold px-4 py-2 rounded-xl font-bold text-sm">
                <i class="fas fa-check-double"></i> Grade Written Answers
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-xl mb-4 font-bold">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 font-bold">
            <i class="fas fa-times-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <table class="min-w-full text-left text-sm whitespace-nowrap">
            <thead class="uppercase tracking-wider border-b border-gray-200 bg-gray-50/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-gray-500 font-bold">Member Name</th>
                    <th scope="col" class="px-6 py-4 text-gray-500 font-bold text-center">Status</th>
                    <th scope="col" class="px-6 py-4 text-gray-500 font-bold text-center">Score (Auto)</th>
                    <th scope="col" class="px-6 py-4 text-gray-500 font-bold text-center">Violations</th>
                    <th scope="col" class="px-6 py-4 text-gray-500 font-bold text-center">Timeline</th>
                    <th scope="col" class="px-6 py-4 text-gray-500 font-bold text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $attempt)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-800">
                        <i class="fas fa-user-circle text-gray-400 mr-2"></i> {{ $attempt->user->name }}
                        @if($attempt->attempt_number > 1)
                            <span class="ml-2 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Retry #{{ $attempt->attempt_number }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($attempt->status == 'submitted')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Submitted</span>
                        @elseif($attempt->status == 'auto_submitted')
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Auto-Submitted (Time Up)</span>
                        @elseif($attempt->status == 'disqualified')
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Disqualified ({{ $attempt->cancelled_reason }})</span>
                        @elseif($attempt->status == 'cancelled')
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold">Cancelled</span>
                        @endif

                        @if($attempt->extra_time_minutes > 0)
                            <br><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 text-xs rounded font-bold mt-1 inline-block">
                                <i class="fas fa-clock mr-1"></i>+{{ $attempt->extra_time_minutes }} min extra time granted
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-black text-lg {{ $attempt->score >= ($quiz->total_marks/2) ? 'text-green-600' : 'text-red-500' }}">
                            {{ $attempt->score }} <span class="text-xs text-gray-400">/ {{ $quiz->total_marks }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs font-bold px-2 py-1 rounded {{ $attempt->violation_count == 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                            {{ $attempt->violation_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-xs text-gray-500">
                        <div>
                            <p><span class="font-bold text-gray-600">Started:</span> {{ $attempt->started_at ? $attempt->started_at->format('M d, h:i A') : 'N/A' }}</p>
                            <p><span class="font-bold text-gray-600">Ended:</span> {{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, h:i A') : 'N/A' }}</p>
                            <p><span class="font-bold text-gray-600">Duration:</span> {{ $quiz->duration_minutes }} min</p>
                            @if($attempt->extra_time_minutes > 0)
                                <p class="text-yellow-700 font-bold mt-1">
                                    +{{ $attempt->extra_time_minutes }} min extra — Granted by admin
                                    @if($attempt->extra_time_granted_at)
                                        at {{ $attempt->extra_time_granted_at->format('h:i A') }}
                                    @endif
                                </p>
                                @if($attempt->extra_time_notes)
                                    <p class="text-xs text-gray-400 italic">{{ $attempt->extra_time_notes }}</p>
                                @endif
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center align-top">
                        @if($attempt->unanswered_questions->count() > 0 && in_array($attempt->status, ['auto_submitted']))
                            <button type="button"
                                onclick="openExtraTimeModal({{ $attempt->id }}, {{ $attempt->unanswered_questions->count() }}, '{{ addslashes($attempt->user->name) }}')"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm">
                                <i class="fas fa-clock mr-1"></i> Grant Extra Time
                                <span class="ml-1 bg-white/30 text-xs px-1.5 rounded">{{ $attempt->unanswered_questions->count() }} unanswered</span>
                            </button>
                            
                            {{-- Hidden form for this attempt --}}
                            <form id="extra-time-form-{{ $attempt->id }}" action="{{ route('admin.quizzes.attempts.extra_time', $attempt->id) }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="extra_minutes" id="extra-minutes-{{ $attempt->id }}">
                                <input type="hidden" name="notes" id="extra-notes-{{ $attempt->id }}">
                            </form>
                        @else
                            <span class="text-gray-300 text-xs font-bold">—</span>
                        @endif
                    </td>
                </tr>

                {{-- Unanswered Questions Sub-row (expandable) --}}
                @if($attempt->unanswered_questions->count() > 0 && in_array($attempt->status, ['auto_submitted']))
                <tr class="bg-amber-50 border-b border-amber-100">
                    <td colspan="6" class="px-8 py-3">
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-widest mb-2">
                            <i class="fas fa-question-circle mr-1"></i> Unanswered Questions ({{ $attempt->unanswered_questions->count() }}) — Time ran out
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($attempt->unanswered_questions as $i => $q)
                                <span class="bg-white border border-amber-200 text-amber-900 text-xs px-3 py-1 rounded-lg font-bold shadow-sm max-w-xs truncate" title="{{ $q->question_text }}">
                                    Q{{ $i+1 }}: {{ Str::limit($q->question_text, 50) }}
                                    <span class="text-amber-400 ml-1">({{ $q->marks }}mk)</span>
                                </span>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif

                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-gray-400 font-bold"><i class="fas fa-folder-open text-3xl mb-2 block"></i> No completed attempts yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Participation Status Modal --}}
<div id="participation-modal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-4xl max-h-[90vh] rounded-3xl overflow-hidden shadow-2xl flex flex-col">
        {{-- Header --}}
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-xl font-black text-gray-900"><i class="fas fa-users text-blue-600 mr-2"></i> Participation Status</h3>
                <p class="text-xs text-gray-400 mt-1">Detailed breakdown of all eligible members for this quiz</p>
            </div>
            <button onclick="closeParticipationModal()" class="text-gray-400 hover:text-black transition text-2xl"><i class="fas fa-times"></i></button>
        </div>

        {{-- Stats and Tabs --}}
        <div class="p-4 bg-white border-b border-gray-100 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex gap-2">
                <button onclick="showParticipationTab('all')" class="participation-tab-btn active px-4 py-2 rounded-xl text-sm font-bold transition" data-tab="all">All Eligible ({{ count($participation['completed']) + count($participation['in_progress']) + count($participation['not_started']) }})</button>
                <button onclick="showParticipationTab('completed')" class="participation-tab-btn px-4 py-2 rounded-xl text-sm font-bold transition text-green-600 bg-green-50" data-tab="completed">Completed ({{ count($participation['completed']) }})</button>
                <button onclick="showParticipationTab('in_progress')" class="participation-tab-btn px-4 py-2 rounded-xl text-sm font-bold transition text-blue-600 bg-blue-50" data-tab="in_progress">In Progress ({{ count($participation['in_progress']) }})</button>
                <button onclick="showParticipationTab('not_started')" class="participation-tab-btn px-4 py-2 rounded-xl text-sm font-bold transition text-gray-500 bg-gray-50" data-tab="not_started">Not Started ({{ count($participation['not_started']) }})</button>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto p-6 bg-gray-50/30">
            <div id="participation-items-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- COMPLETED --}}
                @foreach($participation['completed'] as $item)
                <div class="participation-item p-4 bg-white rounded-2xl border border-green-100 shadow-sm flex items-center justify-between" data-status="completed">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold">
                            {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ $item['user']->name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $item['role'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase text-green-600 block">Submitted</span>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $item['attempt']->submitted_at->format('M d, h:i A') }}</p>
                        <p class="text-[10px] font-bold text-gray-700">Score: {{ $item['attempt']->score }}/{{ $quiz->total_marks }}</p>
                    </div>
                </div>
                @endforeach

                {{-- IN PROGRESS --}}
                @foreach($participation['in_progress'] as $item)
                <div class="participation-item p-4 bg-white rounded-2xl border border-blue-100 shadow-sm flex items-center justify-between" data-status="in_progress">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold animate-pulse">
                            {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ $item['user']->name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $item['role'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase text-blue-600 block">In Progress</span>
                        <p class="text-[10px] text-gray-400 mt-1">Started: {{ $item['attempt']->started_at->format('h:i A') }}</p>
                        <p class="text-[10px] font-bold text-blue-500">Step: {{ $item['attempt']->current_step }}</p>
                    </div>
                </div>
                @endforeach

                {{-- NOT STARTED --}}
                @foreach($participation['not_started'] as $item)
                <div class="participation-item p-4 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between" data-status="not_started">
                    <div class="flex items-center gap-3 opacity-60">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold">
                            {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ $item['user']->name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $item['role'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase text-gray-400 block italic">Not Started</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="p-6 border-t border-gray-100 bg-gray-50/50 text-right">
            <button onclick="closeParticipationModal()" class="px-8 py-3 bg-gray-800 text-white font-bold rounded-xl shadow-lg hover:bg-black transition">Close Window</button>
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
