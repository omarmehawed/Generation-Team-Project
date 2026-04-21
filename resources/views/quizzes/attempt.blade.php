@extends('layouts.exam')

@section('timer')
<div x-data="{ timeString: '00:00:00', critical: false }" 
     @time-update.window="
        let t = $event.detail.time;
        critical = t < 300; // Less than 5 mins
        let h = Math.floor(t / 3600);
        let m = Math.floor((t % 3600) / 60);
        let s = t % 60;
        timeString = (h > 0 ? h.toString().padStart(2, '0') + ':' : '') + 
                     m.toString().padStart(2, '0') + ':' + 
                     s.toString().padStart(2, '0');
     "
     class="flex items-center gap-2 bg-gray-800 px-4 py-2 rounded-xl text-yellow-400 font-mono text-xl font-bold border border-gray-700 shadow-inner"
     :class="{'text-red-500 animate-pulse border-red-500 bg-red-900/30': critical}">
    <i class="fas fa-clock"></i> <span x-text="timeString">00:00:00</span>
</div>
@endsection

@section('content')
<div x-data="examApp()" x-init="init()" class="max-w-4xl mx-auto pb-20">

    <!-- The initialization gate is removed. Exam starts immediately. -->

    <!-- Loading Overlay -->
    <div x-show="loading && hasStarted" x-cloak class="fixed inset-0 bg-white/80 backdrop-blur-sm z-40 flex items-center justify-center">
        <div class="text-center">
            <i class="fas fa-circle-notch fa-spin text-4xl text-yellow-500 mb-4"></i>
            <p class="font-bold text-gray-700">Loading Exam Data...</p>
            <p class="text-xs text-gray-400" x-text="syncStatus"></p>
        </div>
    </div>

    <!-- MAIN EXAM CONTENT WRAPPER -->
    <div x-show="hasStarted" x-cloak>

    <!-- Exam Content Header -->
    <div class="mb-6 flex justify-between items-end border-b pb-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800">{{ $quiz->title }}</h1>
            <p class="text-gray-500 font-bold mt-1">Answer all questions carefully.</p>
        </div>
        <div class="flex items-center space-x-6">
            @if(auth()->user()->hasPermission('manage_quizzes'))
            <form action="{{ route('admin.quizzes.attempts.cancel', $attempt->id) }}" method="POST" class="inline m-0">
                @csrf
                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold py-2 px-4 rounded-lg flex items-center shadow-sm border border-red-200 transition">
                    <i class="fas fa-sign-out-alt mr-2"></i> Exit Test Mode
                </button>
            </form>
            @endif
            <div class="text-right">
                <p class="text-sm font-bold text-gray-500">Progress</p>
                <p class="text-xl font-black text-blue-600">Step <span x-text="currentStep"></span> <span class="text-sm text-gray-400">/ <span x-text="lastPage"></span></span></p>
            </div>
        </div>
    </div>

    <!-- Warning Bar -->
    <div x-show="syncError" x-cloak class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center justify-between">
        <strong class="font-bold"><i class="fas fa-wifi"></i> Connection Error:</strong>
        <span class="block sm:inline ml-2" x-text="syncError"></span>
        <button @click="fetchQuestions(currentStep)" class="ml-4 font-bold underline hover:text-red-900">Retry</button>
    </div>

    <!-- Questions Container -->
    <div class="space-y-6">
        <template x-for="(q, index) in questions" :key="q.id">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-lg">Question <span x-text="((currentStep - 1) * 10) + index + 1"></span></span>
                    <span class="text-xs font-bold text-gray-400"><span x-text="q.marks"></span> Marks <span x-show="q.is_required" class="text-red-500 ml-1">*</span></span>
                </div>
                
                <!-- Text -->
                <p class="text-lg font-bold text-gray-800 mb-6 whitespace-pre-wrap allow-select" x-html="q.question_text"></p>

                <!-- Options -->
                <div x-show="q.question_type === 'mcq'" class="space-y-3">
                    <template x-for="opt in q.options" :key="opt.id">
                        <label class="flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-yellow-50 transition-colors group"
                               :class="{'bg-yellow-100 border-yellow-400 shadow-sm': answers[q.id]?.selected_option_id == opt.id}">
                            <input type="radio" 
                                   :name="'q_' + q.id" 
                                   :value="opt.id"
                                   x-model="answers[q.id].selected_option_id"
                                   @change="saveAnswer(q.id, opt.id, null)"
                                   class="w-5 h-5 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                            <span class="ml-3 font-bold text-gray-700 allow-select" x-text="opt.option_text"></span>
                        </label>
                    </template>
                </div>

                <!-- Written -->
                <div x-show="q.question_type === 'written'">
                    <textarea 
                        rows="5" 
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 font-mono p-4 allow-select" 
                        placeholder="Type your answer here..."
                        x-model="answers[q.id].text_answer"
                        @input.debounce.1000ms="saveAnswer(q.id, null, answers[q.id].text_answer)"></textarea>
                </div>
            </div>
        </template>
        
        <div x-show="questions.length === 0 && !loading" class="text-center py-10 bg-white rounded-2xl shadow-sm">
            <i class="fas fa-check-circle text-5xl text-green-500 mb-4 block"></i>
            <h3 class="text-xl font-bold text-gray-800">No questions found in this step</h3>
        </div>
    </div>

    <!-- Navigation Footer -->
    <div class="mt-8 flex justify-between items-center bg-white p-4 sm:p-6 rounded-2xl shadow border border-gray-100">
        <button @click="prevStep()" :disabled="currentStep === 1 || loading" class="px-6 py-3 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 disabled:opacity-50 transition">
            <i class="fas fa-chevron-left mr-2"></i> Previous
        </button>
        
        <div class="flex items-center gap-2">
            <!-- Paginator Dots -->
            <template x-for="i in lastPage">
                <div class="w-3 h-3 rounded-full transition-colors" :class="i === currentStep ? 'bg-blue-600' : 'bg-gray-200'"></div>
            </template>
        </div>

        <button x-show="currentStep < lastPage" @click="nextStep()" :disabled="loading" class="px-6 py-3 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 transition shadow-md">
            Next <i class="fas fa-chevron-right ml-2"></i>
        </button>

        <button x-show="currentStep === lastPage && lastPage > 0" @click="submitExam()" :disabled="loading" class="btn-royal-gold px-8 py-3 rounded-xl font-black text-lg shadow-xl uppercase tracking-widest">
            Submit Exam
        </button>
    </div>

    </div>

</div>

@push('scripts')
<script>
    const CONFIG = {
        quizId: {{ $quiz->id }},
        requireFullscreen: {{ $quiz->require_fullscreen ? 'true' : 'false' }},
        autoCancelCopy: {{ $quiz->auto_cancel_on_copy ? 'true' : 'false' }},
        autoCancelPaste: {{ $quiz->auto_cancel_on_paste ? 'true' : 'false' }},
        csrfToken: document.querySelector('meta[name="csrf-token"]').content,
        urls: {
            step: '{{ route("quizzes.api.step", $quiz->id) }}',
            save: '{{ route("quizzes.api.save", $quiz->id) }}',
            submit: '{{ route("quizzes.api.submit", $quiz->id) }}',
            violate: '{{ route("quizzes.api.violation", $quiz->id) }}',
            redirect: '{{ route("quizzes.show", $quiz->id) }}'
        }
    };

    function examApp() {
        return {
            loading: false,
            hasStarted: false,
            questions: [],
            answers: {}, // { question_id: { selected_option_id: null, text_answer: '' } }
            currentStep: {{ $attempt->current_step ?? 1 }},
            lastPage: 1,
            timeRemaining: -1, // -1 = not yet loaded from server
            timeInterval: null,
            syncStatus: '',
            syncError: null,
            submitting: false,

            async init() {
                if (CONFIG.requireFullscreen) {
                    // Attempt fullscreen — never block exam if it fails
                    try {
                        const el = document.documentElement;
                        if (el.requestFullscreen) await el.requestFullscreen().catch(() => {});
                        else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
                        else if (el.mozRequestFullScreen) el.mozRequestFullScreen();
                    } catch (e) {
                        // Fullscreen unavailable — exam continues normally
                        console.warn('Fullscreen unavailable:', e);
                    }
                }
                this.startActualExam();
            },

            async startActualExam() {
                this.hasStarted = true;
                this.setupAntiCheat();
                
                // Fetch questions FIRST - startTimer is called inside fetchQuestions
                // after the server confirms the remaining time
                await this.fetchQuestions(this.currentStep);
                // Timer is started inside fetchQuestions once timeRemaining is populated
            },

            async fetchQuestions(step) {
                this.loading = true;
                this.syncError = null;
                this.syncStatus = 'Loading questions...';
                
                try {
                    const res = await fetch(`${CONFIG.urls.step}?step=${step}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if(res.status === 403) {
                        window.location.href = CONFIG.urls.redirect;
                        return;
                    }
                    if(!res.ok) throw new Error('Network error');
                    
                    const data = await res.json();
                    
                    this.questions = data.questions;
                    this.currentStep = data.current_page;
                    this.lastPage = data.last_page;
                    
                    // Initialize answers object cleanly
                    this.questions.forEach(q => {
                        let existing = data.answers[q.id];
                        this.answers[q.id] = {
                            selected_option_id: existing ? existing.selected_option_id : null,
                            text_answer: existing ? existing.text_answer : ''
                        };
                    });

                    // Update local timer from server (authoritative source)
                    const serverTime = parseInt(data.time_remaining);
                    this.timeRemaining = (serverTime > 0) ? serverTime : 0;

                    // Start timer only on initial page load (timeRemaining was -1)
                    // On step navigation, just sync the time but don't restart the interval
                    if (this.timeInterval === null) {
                        if (this.timeRemaining <= 0) {
                            // Server says time is already up
                            this.doSubmit();
                        } else {
                            this.startTimer();
                        }
                    }

                } catch (err) {
                    console.error(err);
                    this.syncError = "Failed to load questions. Please check your connection.";
                } finally {
                    this.loading = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            async saveAnswer(question_id, selected_option_id, text_answer) {
                this.syncError = null;
                try {
                    const formData = new FormData();
                    formData.append('question_id', question_id);
                    if(selected_option_id) formData.append('selected_option_id', selected_option_id);
                    if(text_answer !== null && text_answer !== undefined) formData.append('text_answer', text_answer);

                    const res = await fetch(CONFIG.urls.save, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CONFIG.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    
                    if(!res.ok) throw new Error();
                } catch (err) {
                    this.syncError = "Failed to auto-save answer. Please check your connection.";
                }
            },

            nextStep() {
                if (this.currentStep < this.lastPage) {
                    this.fetchQuestions(this.currentStep + 1);
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.fetchQuestions(this.currentStep - 1);
                }
            },

            async submitExam() {
                if(this.submitting) return;

                const confirmed = await Swal.fire({
                    title: 'Submit Exam?',
                    text: 'Are you sure you want to completely finish and submit the exam?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Submit'
                });

                if(confirmed.isConfirmed) {
                    this.doSubmit();
                }
            },

            async doSubmit() {
                this.submitting = true;
                this.loading = true;
                this.syncStatus = 'Finalizing submission...';

                try {
                    const res = await fetch(CONFIG.urls.submit, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CONFIG.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await res.json();
                    
                    if(res.ok && data.success) {
                        window.onbeforeunload = null; // Remove prompt
                        // Go to the dedicated result/review page
                        window.location.replace(data.redirect || CONFIG.urls.redirect);
                    } else {
                        throw new Error();
                    }
                } catch(e) {
                    this.submitting = false;
                    this.loading = false;
                    Swal.fire('Error', 'Failed to submit. Try again.', 'error');
                }
            },

            startTimer() {
                // Guard: never start if timeInterval already running
                if(this.timeInterval) return;
                
                // Dispatch initial time immediately so UI doesn't show 00:00:00 for 1s
                window.dispatchEvent(new CustomEvent('time-update', { detail: { time: this.timeRemaining }}));

                this.timeInterval = setInterval(() => {
                    this.timeRemaining--;
                    
                    if(this.timeRemaining <= 0) {
                        clearInterval(this.timeInterval);
                        this.timeInterval = null;
                        this.timeRemaining = 0;
                        Swal.fire({
                            title: 'Time is Up!',
                            text: 'Your exam is being automatically submitted.',
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                        });
                        this.doSubmit();
                    }
                    
                    window.dispatchEvent(new CustomEvent('time-update', { detail: { time: this.timeRemaining }}));
                }, 1000);
            },

            setupAntiCheat() {
                const self = this;
                
                // Handler to report violation to backend
                const report = async (type, details) => {
                    if(self.submitting) return; // Prevent reports during graceful shutdown
                    try {
                        const res = await fetch(CONFIG.urls.violate, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': CONFIG.csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ violation_type: type, details: details })
                        });
                        const data = await res.json();
                        
                        if(data.cancelled) {
                            window.onbeforeunload = null;
                            Swal.fire({
                                title: 'Disqualified',
                                text: 'Your exam attempt has been terminated due to serious rules violation.',
                                icon: 'error',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                            });
                            // Redirect
                            setTimeout(() => { window.location.replace(data.redirect); }, 3000);
                        } else {
                            Toast.fire({
                                icon: 'warning',
                                title: 'Warning recorded!',
                                text: `Violation ${data.violations} registered.`
                            });
                        }
                    } catch (e) { console.error("Violation report failed"); }
                };

                // 1. Fullscreen Monitoring (Conditional)
                if (CONFIG.requireFullscreen) {
                    const handleFullscreenExit = () => {
                        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement) {
                            if (self.hasStarted && !self.submitting) {
                                report('fullscreen_exit', 'Exited fullscreen mode.');
                            }
                        }
                    };
                    document.addEventListener('fullscreenchange', handleFullscreenExit, { passive: true });
                    document.addEventListener('webkitfullscreenchange', handleFullscreenExit, { passive: true });
                    document.addEventListener('mozfullscreenchange', handleFullscreenExit, { passive: true });
                    document.addEventListener('MSFullscreenChange', handleFullscreenExit, { passive: true });
                }

                // 2. Visibility / Focus tracking
                window.addEventListener('blur', () => {
                    report('blur', 'Window lost focus.');
                    document.body.style.opacity = '0'; // Hide content instantly
                });
                window.addEventListener('focus', () => {
                    document.body.style.opacity = '1';
                });
                
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden && self.hasStarted && !self.submitting) {
                        report('visibility_hidden', 'Switched to another tab or minimized.');
                    }
                });

                // 4. Copy/Paste Blocking
                document.addEventListener('copy', (e) => {
                    e.preventDefault();
                    report('copy', 'Attempted to copy exam text.');
                    Toast.fire({ icon: 'error', title: 'Copying is strictly prohibited!' });
                });
                
                document.addEventListener('paste', (e) => {
                    e.preventDefault();
                    report('paste', 'Attempted to paste text.');
                    Toast.fire({ icon: 'error', title: 'Pasting is strictly prohibited!' });
                });

                // 5. Prevent Unload Warning
                window.onbeforeunload = function() {
                    if (!self.submitting && self.hasStarted) {
                        return "If you reload or leave, this will record a violation. Are you sure?";
                    }
                };
            }
        }
    }

    // Reuse Toast from batu
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
</script>
@endpush
@endsection
