<div class="bg-white rounded-3xl mb-12 shadow-xl border border-gray-100 relative overflow-hidden transition-all duration-300 hover:shadow-2xl"
    id="weekly-evaluation-card" x-data="{ open: false }">

    {{-- Header / Trigger --}}
    <div @click="open = !open"
        class="p-6 cursor-pointer flex justify-between items-center relative z-10 bg-white hover:bg-gray-50 transition-colors">

        <div class="flex items-center gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <i class="fas fa-clipboard-check text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900">Weekly Evaluation</h2>
                <p class="text-gray-500 text-xs mt-0.5">Click to expand/collapse evaluation form</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            {{-- Week Selector (Moved here for quick access even when closed? Or keep inside? User said "compact". Let's
            keep it inside or right here if needed.
            Better to keep week selector visible only when open or maybe in header?
            Let's put it in the header for efficiency so they can select week before opening?
            No, user wants "compact". Let's put chevron here. --}}
            <div class="transform transition-transform duration-300" :class="{'rotate-180': open}">
                <i class="fas fa-chevron-down text-gray-400"></i>
            </div>
        </div>
    </div>

    {{-- Collapsible Content --}}
    <div x-show="open" x-collapse x-cloak class="border-t border-gray-100">
        <div class="p-8 relative">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-10 -mt-10 opacity-50 pointer-events-none">
            </div>

            <div class="flex justify-between items-center mb-8 relative z-10">
                <div>
                    {{-- Subtitle or instructions --}}
                    <p class="text-gray-500 text-sm">Select a week to assess performance.</p>
                </div>

                {{-- Week Selector --}}
                <div class="flex items-center gap-3" @click.stop> {{-- Stop propagation to prevent closing when clicking
                    selector --}}
                    <label class="text-xs font-bold text-gray-500 uppercase">Select Week:</label>
                    <select id="week-selector" onchange="loadEvaluationForm(this.value)"
                        class="bg-gray-50 border-2 border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-bold outline-none transition hover:border-blue-300 cursor-pointer">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('week', 1) == $i ? 'selected' : '' }}>Week {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Content Container --}}
            <div id="evaluation-form-container" class="relative min-h-[400px]">
                {{-- Loading Spinner --}}
                <div class="absolute inset-0 items-center justify-center bg-white/80 z-20 hidden" id="loading-spinner">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>

                {{-- Form Content --}}
                <div id="evaluation-content">
                    {{-- Loaded via AJAX --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadEvaluationForm(1);
    });

    function loadEvaluationForm(week) {
        const container = document.getElementById('evaluation-content');
        const spinner = document.getElementById('loading-spinner');
        const studentId = "{{ $user->id }}";

        spinner.classList.remove('hidden');
        spinner.classList.add('flex');

        fetch(`/weekly-evaluation/get/${studentId}/${week}`)
            .then(response => response.json())
            .then(data => {
                container.innerHTML = data.html;
                initializeDynamicFields(); // Re-init specific JS if needed
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<p class="text-red-500 text-center py-10">Failed to load evaluation data.</p>';
            })
            .finally(() => {
                spinner.classList.add('hidden');
                spinner.classList.remove('flex');
            });
    }

    function initializeDynamicFields() {
        // Any JS initialization for the loaded form (e.g., repeaters)
    }

    // Function to submit the form via AJAX
    function submitEvaluation(form) {
        const spinner = document.getElementById('loading-spinner');
        const formData = new FormData(form);
        const week = document.getElementById('week-selector').value;
        const studentId = "{{ $user->id }}"; // Ensure student ID is passed

        // Append if not in form
        if (!formData.has('student_id')) formData.append('student_id', studentId);
        if (!formData.has('week_number')) formData.append('week_number', week);

        spinner.classList.remove('hidden');
        spinner.classList.add('flex');

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
            .then(async response => {
                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : null;

                if (!response.ok) {
                    // Handle Validation Errors (422)
                    if (response.status === 422) {
                        let errorMsg = 'Validation Error:\n';
                        for (const [key, messages] of Object.entries(data.errors)) {
                            errorMsg += `${messages.join(', ')}\n`;
                        }
                        throw new Error(errorMsg);
                    }
                    // Handle other server errors
                    throw new Error(data?.message || response.statusText || 'Server Error');
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Evaluation saved successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    // Reload to show updated data/PDF link
                    loadEvaluationForm(week);
                } else {
                    throw new Error(data.message || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Submission Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: error.message,
                });
            })
            .finally(() => {
                spinner.classList.add('hidden');
                spinner.classList.remove('flex');
            });

        return false; // Prevent default submission
    }
</script>