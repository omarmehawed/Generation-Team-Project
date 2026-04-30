{{-- Evaluation Modal Component --}}
<div id="evaluationModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-md" onclick="closeModal('evaluationModal')"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] w-full max-w-2xl overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300 transform border border-gray-100 dark:border-gray-700 flex flex-col" id="evaluationModalContent" style="max-height: 90vh;">
        
        {{-- Header --}}
        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 shrink-0">
            <div>
                <h3 class="text-xl font-black text-gray-800 dark:text-gray-200" id="evalModalTitle">Evaluating</h3>
                <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest mt-1" id="evalWeekLabel">Week #{{ $currentPeriod->week_number ?? '?' }} Evaluation</p>
            </div>
            <button onclick="closeModal('evaluationModal')" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('evaluation.store', $team->id) }}" method="POST" id="evalForm" class="flex flex-col overflow-hidden h-full">
            @csrf
            <input type="hidden" name="evaluatee_id" id="evalTargetId">
            <input type="hidden" name="evaluation_type" id="evalTargetType">
            <input type="hidden" name="has_tasks" id="hasTasksInput">
            <input type="hidden" name="has_workshops" id="hasWorkshopsInput">
            <input type="hidden" name="has_meetings" id="hasMeetingsInput">
            
            <div class="p-8 space-y-6 overflow-y-auto custom-scroll bg-white dark:bg-gray-800 flex-1">

                {{-- Loading Indicator --}}
                <div id="evalLoadingState" class="flex items-center justify-center py-8">
                    <div class="w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">Loading activity data...</span>
                </div>

                {{-- === TASK SCORING === --}}
                <div id="evalTaskSection" class="hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-blue-600 rounded-xl text-white flex items-center justify-center shadow">
                            <i class="fas fa-tasks text-sm"></i>
                        </div>
                        <h4 class="font-black text-gray-800 dark:text-gray-200">Task Score</h4>
                    </div>
                    <div id="evalTaskList" class="space-y-3"></div>
                    <div class="mt-4 p-4 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-blue-700">Task Score</span>
                        <div class="flex items-center gap-2">
                            <input type="number" name="task_score" id="taskScoreInput" min="0" max="10" step="0.5" value="0"
                                class="w-20 rounded-xl border border-blue-200 bg-white dark:bg-gray-800 px-3 py-1.5 text-center text-sm font-black text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="updateTotalScore()">
                            <span class="text-sm text-gray-500 dark:text-gray-400">/ 10</span>
                        </div>
                    </div>
                </div>

                {{-- No tasks placeholder (Hidden by default, only shown if explicitly needed) --}}
                <div id="evalNoTasks" class="hidden">
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-center">
                        <i class="fas fa-inbox text-gray-400 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-widest">No tasks assigned this week</p>
                    </div>
                </div>

                {{-- === WORKSHOP SCORING === --}}
                <div id="evalWorkshopSection" class="hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-amber-500 rounded-xl text-white flex items-center justify-center shadow">
                            <i class="fas fa-chalkboard-teacher text-sm"></i>
                        </div>
                        <h4 class="font-black text-gray-800 dark:text-gray-200">Workshop Score</h4>
                    </div>
                    <div id="evalWorkshopList" class="space-y-3"></div>
                    <div class="mt-4 p-4 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-amber-700">Workshop Score</span>
                        <div class="flex items-center gap-2">
                            <input type="number" name="workshop_score" id="workshopScoreInput" min="0" max="10" step="0.5" value="0"
                                class="w-20 rounded-xl border border-amber-200 bg-white dark:bg-gray-800 px-3 py-1.5 text-center text-sm font-black text-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500"
                                onchange="updateTotalScore()">
                            <span class="text-sm text-gray-500 dark:text-gray-400">/ 10</span>
                        </div>
                    </div>
                </div>

                {{-- No workshops placeholder --}}
                <div id="evalNoWorkshops" class="hidden">
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-center">
                        <i class="fas fa-chalkboard text-gray-400 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-widest">No workshops relevant this week</p>
                    </div>
                </div>

                {{-- === MEETING SCORING === --}}
                <div id="evalMeetingSection" class="hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 bg-green-600 rounded-xl text-white flex items-center justify-center shadow">
                            <i class="fas fa-users text-sm"></i>
                        </div>
                        <h4 class="font-black text-gray-800 dark:text-gray-200">Meeting Attendance Score</h4>
                    </div>
                    <div id="evalMeetingList" class="space-y-3"></div>
                    <div class="mt-4 p-4 rounded-2xl bg-green-50 border border-green-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-green-700">Meeting Score</span>
                        <div class="flex items-center gap-2">
                            <input type="number" name="meeting_score" id="meetingScoreInput" min="0" max="10" step="0.5" value="0"
                                class="w-20 rounded-xl border border-green-200 bg-white dark:bg-gray-800 px-3 py-1.5 text-center text-sm font-black text-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                                onchange="updateTotalScore()">
                            <span class="text-sm text-gray-500 dark:text-gray-400">/ 10</span>
                        </div>
                    </div>
                </div>

                {{-- No meetings placeholder --}}
                <div id="evalNoMeetings" class="hidden">
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-center">
                        <i class="fas fa-calendar-times text-gray-400 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-widest">No meetings relevant this week</p>
                    </div>
                </div>

                {{-- Total Score Display --}}
                <div class="p-5 rounded-2xl bg-indigo-600 text-white flex items-center justify-between shadow-xl shadow-indigo-500/20">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest opacity-70">Total Score</p>
                        <p class="text-3xl font-black mt-1" id="totalScoreDisplay">0</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs opacity-70 font-bold">Out of</p>
                        <p class="text-3xl font-black" id="possibleScoreDisplay">30</p>
                    </div>
                </div>

                {{-- Feedback Section --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Constructive Feedback</label>
                    <textarea name="general_notes" id="evalGeneralNotes"
                        class="w-full rounded-3xl border border-gray-100 dark:border-gray-700 focus:border-indigo-500 focus:ring-0 bg-gray-50/50 p-6 text-sm font-medium transition-all text-gray-800 dark:text-gray-200 placeholder-gray-400 min-h-[100px]" 
                        placeholder="What should this member improve or keep doing?"></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 dark:border-gray-700 flex justify-end items-center gap-4 shrink-0">
                <button type="button" onclick="closeModal('evaluationModal')" 
                    class="px-8 py-3 rounded-2xl font-bold text-gray-400 hover:bg-gray-100 transition-colors text-sm uppercase tracking-widest">Discard</button>
                <button type="submit" 
                    class="px-10 py-3 rounded-2xl font-black text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-3 text-sm uppercase tracking-widest">
                    Submit Review <i class="fas fa-paper-plane text-xs opacity-50"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .dark .custom-scroll::-webkit-scrollbar-thumb { background: #1e293b; }
    body.modal-open { overflow: hidden; }
</style>

<script>
    function getScoreInputValue(primaryId) {
        const el = document.getElementById(primaryId);
        if (el && !el.closest('.hidden')) return parseFloat(el.value) || 0;
        return 0;
    }

    function updateTotalScore() {
        const t = getScoreInputValue('taskScoreInput');
        const w = getScoreInputValue('workshopScoreInput');
        const m = getScoreInputValue('meetingScoreInput');
        document.getElementById('totalScoreDisplay').innerText = (t + w + m).toFixed(1);
        
        let possible = 0;
        if (!document.getElementById('evalTaskSection').classList.contains('hidden')) possible += 10;
        if (!document.getElementById('evalWorkshopSection').classList.contains('hidden')) possible += 10;
        if (!document.getElementById('evalMeetingSection').classList.contains('hidden')) possible += 10;
        
        if (possible === 0) {
           // If somehow NOTHING is assigned, default to 30 or show warning
           possible = 30;
        }
        document.getElementById('possibleScoreDisplay').innerText = possible;
    }

    function syncScoreInputs(primaryId) {
        const primary = document.getElementById(primaryId);
        if (primary) primary.addEventListener('change', () => { updateTotalScore(); });
    }

    syncScoreInputs('taskScoreInput');
    syncScoreInputs('workshopScoreInput');
    syncScoreInputs('meetingScoreInput');

    function openEvaluationModal(id, name, type, periodId, existingRecord = null) {
        // Show modal
        document.getElementById('evaluationModal').classList.remove('hidden');
        document.getElementById('evaluationModal').classList.add('flex');
        setTimeout(() => {
            document.getElementById('evaluationModalContent').classList.remove('scale-95', 'opacity-0');
        }, 10);
        
        document.getElementById('evalTargetId').value = id;
        document.getElementById('evalTargetType').value = type;
        document.getElementById('evalModalTitle').innerText = 'Evaluating: ' + name;
        document.body.classList.add('modal-open');

        // Show loading state
        document.getElementById('evalLoadingState').classList.remove('hidden');
        ['evalTaskSection','evalNoTasks','evalWorkshopSection','evalNoWorkshops','evalMeetingSection','evalNoMeetings'].forEach(s => {
            document.getElementById(s).classList.add('hidden');
        });
        
        // Reset inputs
        ['taskScoreInput','workshopScoreInput','meetingScoreInput'].forEach(i => {
            const el = document.getElementById(i);
            if (el) el.value = 0;
        });
        
        document.getElementById('evalGeneralNotes').value = '';
        document.getElementById('totalScoreDisplay').innerText = '0';
        document.getElementById('possibleScoreDisplay').innerText = '30';
        
        document.getElementById('hasTasksInput').value = '0';
        document.getElementById('hasWorkshopsInput').value = '0';
        document.getElementById('hasMeetingsInput').value = '0';

        // Pre-fill if editing existing record
        if (existingRecord) {
            document.getElementById('taskScoreInput').value = existingRecord.task_score || 0;
            document.getElementById('workshopScoreInput').value = existingRecord.workshop_score || 0;
            document.getElementById('meetingScoreInput').value = existingRecord.meeting_score || 0;
            document.getElementById('evalGeneralNotes').value = existingRecord.notes || '';
            // Note: We'll call updateTotalScore AFTER fetching auto-score to know which sections are visible
        }

        // Fetch auto-score data using the passed periodId
        fetch(`/evaluation/team/{{ $team->id }}/${id}/auto-score?period_id=${periodId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('evalLoadingState').classList.add('hidden');
                
                document.getElementById('hasTasksInput').value = data.has_tasks ? '1' : '0';
                document.getElementById('hasWorkshopsInput').value = data.has_workshops ? '1' : '0';
                document.getElementById('hasMeetingsInput').value = data.has_meetings ? '1' : '0';

                // ---- TASKS ----
                if (data.tasks && data.tasks.length > 0) {
                    document.getElementById('evalTaskSection').classList.remove('hidden');
                    let taskHtml = '';
                    data.tasks.forEach(t => {
                        const statusColor = t.status === 'completed' || t.status === 'approved' ? 'text-green-600' : (t.status === 'rejected' ? 'text-red-600' : (t.status === 'reviewing' ? 'text-amber-600' : 'text-gray-400'));
                        const statusIcon = t.status === 'completed' || t.status === 'approved' ? 'fa-check-circle' : (t.status === 'rejected' ? 'fa-times-circle' : (t.status === 'reviewing' ? 'fa-clock' : 'fa-minus-circle'));
                        
                        let penaltyLabel = '';
                        if (t.is_late) penaltyLabel = `<span class="bg-red-50 text-red-600 text-[9px] font-black px-2 py-0.5 rounded-full border border-red-100 uppercase tracking-tighter">LATE -2</span>`;
                        else if (t.is_missing) penaltyLabel = `<span class="bg-red-100 text-red-700 text-[9px] font-black px-2 py-0.5 rounded-full border border-red-200 uppercase tracking-tighter">MISSING -2</span>`;

                        let viewBtn = '';
                        if (t.submission_type === 'file' && t.submission_file) {
                            viewBtn = `<a href="${t.submission_file}" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-[10px] font-black hover:bg-indigo-100 transition-all shadow-sm border border-indigo-100">
                                <i class="fas fa-eye"></i> View Task
                            </a>`;
                        } else if (t.submission_type === 'link' && t.submission_value) {
                            viewBtn = `<a href="${t.submission_value}" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-[10px] font-black hover:bg-indigo-100 transition-all shadow-sm border border-indigo-100">
                                <i class="fas fa-link"></i> View Link
                            </a>`;
                        }

                        taskHtml += `
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50/50 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-white dark:bg-gray-800 shadow-sm">
                                        <i class="fas ${statusIcon} ${statusColor} text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 leading-tight">${t.title}</span>
                                            ${penaltyLabel}
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full ${t.status ==='approved' || t.status === 'completed' ? 'bg-green-500' : (t.status === 'rejected' ? 'bg-red-500' : 'bg-amber-500')}"></span>
                                            <span class="text-[9px] font-black uppercase tracking-wider text-gray-400">${t.status}</span>
                                        </div>
                                    </div>
                                </div>
                                ${viewBtn}
                            </div>`;
                    });
                    document.getElementById('evalTaskList').innerHTML = taskHtml;
                    if (!existingRecord) {
                        document.getElementById('taskScoreInput').value = data.suggested_task_score || 0;
                        document.getElementById('taskScoreInputFallback').value = data.suggested_task_score || 0;
                    }
                } else {
                    document.getElementById('evalNoTasks').classList.remove('hidden');
                }

                // ---- WORKSHOPS ----
                if (data.workshops && data.workshops.length > 0) {
                    document.getElementById('evalWorkshopSection').classList.remove('hidden');
                    let wsHtml = '';
                    data.workshops.forEach(w => {
                        const isAbsent = w.status === 'absent';
                        const isAttended = w.status === 'attended' || w.status === 'late';
                        
                        const bgClass = isAbsent ? 'bg-red-50 border border-red-100' : 'bg-gray-50/50 border border-gray-100';
                        const iconClass = isAbsent ? 'fa-times-circle text-red-500' : 'fa-check-circle text-green-500';
                        const scoreLabel = isAttended ? `Score: ${w.score}` : 'Absent → 0';

                        wsHtml += `
                            <div class="flex items-center justify-between p-3 rounded-2xl ${bgClass}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-white dark:bg-gray-800 shadow-sm">
                                        <i class="fas ${iconClass} text-sm"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">${w.title}</span>
                                </div>
                                <span class="text-[10px] font-black uppercase ${isAbsent ?'text-red-500' : 'text-indigo-600'}">${scoreLabel}</span>
                            </div>`;
                    });
                    document.getElementById('evalWorkshopList').innerHTML = wsHtml;
                    if (!existingRecord) {
                        document.getElementById('workshopScoreInput').value = data.suggested_workshop_score || 0;
                        document.getElementById('workshopScoreInputFallback').value = data.suggested_workshop_score || 0;
                    }
                } else {
                    document.getElementById('evalNoWorkshops').classList.remove('hidden');
                }


                // ---- MEETINGS ----
                if (data.meetings && data.meetings.length > 0) {
                    document.getElementById('evalMeetingSection').classList.remove('hidden');
                    let mtHtml = '';
                    data.meetings.forEach(m => {
                        const isAbsent = m.status === 'absent';
                        mtHtml += `
                            <div class="flex items-center justify-between p-3 rounded-2xl ${isAbsent ?'bg-red-50 border border-red-100' : 'bg-gray-50/50 border border-gray-100'}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-white dark:bg-gray-800 shadow-sm">
                                        <i class="fas ${isAbsent ?'fa-times-circle text-red-500' : 'fa-check-circle text-green-500'} text-sm"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">${m.topic}</span>
                                </div>
                                <span class="text-[10px] font-black uppercase ${isAbsent ?'text-red-500' : 'text-green-600'}">${isAbsent ? 'ABSENT → 0' : 'ATTENDED'}</span>
                            </div>`;
                    });
                    document.getElementById('evalMeetingList').innerHTML = mtHtml;
                    if (!existingRecord) {
                        document.getElementById('meetingScoreInput').value = data.suggested_meeting_score || 0;
                        document.getElementById('meetingScoreInputFallback').value = data.suggested_meeting_score || 0;
                    }
                } else {
                    document.getElementById('evalNoMeetings').classList.remove('hidden');
                }

                updateTotalScore();
            })
            .catch(err => {
                console.error("Score fetch error", err);
                document.getElementById('evalLoadingState').classList.add('hidden');
                document.getElementById('evalNoTasks').classList.remove('hidden');
                document.getElementById('evalNoWorkshops').classList.remove('hidden');
                document.getElementById('evalNoMeetings').classList.remove('hidden');
            });
    }

    function closeModal(id) {
        const content = document.getElementById(id + 'Content');
        if (content) content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
            document.body.classList.remove('modal-open');
        }, 300);
    }
</script>
