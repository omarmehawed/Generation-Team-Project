@extends('layouts.batu')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('admin.quizzes.index') }}" class="text-gray-500 hover:text-black font-bold text-sm"><i class="fas fa-arrow-left"></i> Back to Quizzes</a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <h1 class="text-2xl font-black text-gray-800 mb-6 border-b pb-4"><i class="fas fa-cog text-orange-500"></i> Edit Quiz: {{ $quiz->title }}</h1>

        <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Quiz Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ $quiz->title }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">{{ $quiz->description }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Duration (Minutes) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_minutes" required min="1" value="{{ $quiz->duration_minutes }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Total Marks <span class="text-red-500">*</span></label>
                    <input type="number" name="total_marks" required min="0" value="{{ $quiz->total_marks }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Available From</label>
                    <input type="datetime-local" name="start_at" value="{{ $quiz->start_at ? $quiz->start_at->format('Y-m-d\TH:i') : '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Available Until</label>
                    <input type="datetime-local" name="end_at" value="{{ $quiz->end_at ? $quiz->end_at->format('Y-m-d\TH:i') : '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6 mt-6">
                <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-users text-blue-600"></i> Assign Quiz To</h3>
                @php
                    $rawRoles = is_string($quiz->targeted_roles) ? json_decode($quiz->targeted_roles, true) : $quiz->targeted_roles;
                    $rolesArr = $rawRoles ?? ['all'];
                @endphp

                <div class="space-y-3 bg-blue-50 p-4 rounded-xl border border-blue-100 max-h-64 overflow-y-auto">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="targeted_roles[]" value="all" {{ in_array('all', $rolesArr) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 targeted-role-all">
                        <span class="font-bold text-gray-800 text-sm">All Team Members</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="targeted_roles[]" value="tech:software" {{ in_array('tech:software', $rolesArr) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 targeted-role-spec">
                        <span class="font-semibold text-gray-700 text-sm">Software Team Only</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="targeted_roles[]" value="tech:hardware" {{ in_array('tech:hardware', $rolesArr) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 targeted-role-spec">
                        <span class="font-semibold text-gray-700 text-sm">Hardware Team Only</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="targeted_roles[]" value="role:sub_leader" {{ in_array('role:sub_leader', $rolesArr) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 targeted-role-spec">
                        <span class="font-semibold text-gray-700 text-sm">All Sub-Leaders</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="targeted_roles[]" value="tech:software|role:sub_leader" {{ in_array('tech:software|role:sub_leader', $rolesArr) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 targeted-role-spec">
                        <span class="font-semibold text-gray-700 text-sm">Software Sub-Leaders</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="targeted_roles[]" value="tech:hardware|role:sub_leader" {{ in_array('tech:hardware|role:sub_leader', $rolesArr) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 targeted-role-spec">
                        <span class="font-semibold text-gray-700 text-sm">Hardware Sub-Leaders</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="targeted_roles[]" value="role:vice_leader" {{ in_array('role:vice_leader', $rolesArr) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 targeted-role-spec">
                        <span class="font-semibold text-gray-700 text-sm">All Vice Leaders</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="targeted_roles[]" value="tech:software|role:vice_leader" {{ in_array('tech:software|role:vice_leader', $rolesArr) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 targeted-role-spec">
                        <span class="font-semibold text-gray-700 text-sm">Software Vice Leaders</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="targeted_roles[]" value="tech:hardware|role:vice_leader" {{ in_array('tech:hardware|role:vice_leader', $rolesArr) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 targeted-role-spec">
                        <span class="font-semibold text-gray-700 text-sm">Hardware Vice Leaders</span>
                    </label>
                </div>
                <script>
                    document.querySelectorAll('.targeted-role-all').forEach(el => {
                        el.addEventListener('change', function() {
                            if (this.checked) {
                                document.querySelectorAll('.targeted-role-spec').forEach(cb => cb.checked = false);
                            }
                        });
                    });
                    document.querySelectorAll('.targeted-role-spec').forEach(el => {
                        el.addEventListener('change', function() {
                            if (this.checked) {
                                document.querySelectorAll('.targeted-role-all').forEach(cb => cb.checked = false);
                            }
                        });
                    });
                </script>
            </div>

            {{-- ===== ANTI-CHEAT & EXAM BEHAVIOR ===== --}}
            <div class="border-t border-gray-100 pt-6 mt-6">
                <h3 class="font-bold text-gray-800 mb-1"><i class="fas fa-shield-alt text-yellow-600"></i> Anti-Cheat & Exam Behavior</h3>
                <p class="text-xs text-gray-400 mb-4">All options are optional. Enable only what you need for this quiz.</p>

                {{-- Fullscreen --}}
                <div class="mb-3 bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <p class="text-xs font-black uppercase tracking-widest text-orange-600 mb-3"><i class="fas fa-expand mr-1"></i> Fullscreen</p>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="require_fullscreen" value="1" {{ $quiz->require_fullscreen ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-orange-500 rounded border-gray-300 focus:ring-orange-400">
                        <div>
                            <span class="font-bold text-gray-700 text-sm block">Require Fullscreen</span>
                            <span class="text-xs text-gray-500">The quiz will enter fullscreen before the attempt. Exiting fullscreen counts as a violation. Disable if your browser has conflicts.</span>
                        </div>
                    </label>
                </div>

                {{-- Shuffle --}}
                <div class="mb-3 bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <p class="text-xs font-black uppercase tracking-widest text-purple-600 mb-3"><i class="fas fa-random mr-1"></i> Shuffle (Anti-Cooperation)</p>
                    <div class="space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="shuffle_questions" value="1" {{ $quiz->shuffle_questions ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-purple-600 rounded border-gray-300 focus:ring-purple-400">
                            <div>
                                <span class="font-bold text-gray-700 text-sm block">Shuffle Question Order per Member</span>
                                <span class="text-xs text-gray-500">Each attempt receives a unique randomized question order, generated once at start and stable across page refreshes.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="shuffle_options" value="1" {{ $quiz->shuffle_options ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-purple-600 rounded border-gray-300 focus:ring-purple-400">
                            <div>
                                <span class="font-bold text-gray-700 text-sm block">Shuffle Answer Options per Question</span>
                                <span class="text-xs text-gray-500">The A/B/C/D answer choices appear in a different order for each member on each question.</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Violations --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <p class="text-xs font-black uppercase tracking-widest text-gray-500 mb-3"><i class="fas fa-ban mr-1"></i> Violations & Auto-Disqualify</p>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="auto_cancel_on_copy" value="1" {{ $quiz->auto_cancel_on_copy ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-yellow-500">
                            <span class="font-bold text-gray-700 text-sm">Auto-Disqualify on Copy Text</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="auto_cancel_on_paste" value="1" {{ $quiz->auto_cancel_on_paste ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-yellow-500">
                            <span class="font-bold text-gray-700 text-sm">Auto-Disqualify on Paste Text</span>
                        </label>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 mt-2">Max blur/unfocus violations before disqualification:</label>
                            <input type="number" name="max_violations" min="1" value="{{ $quiz->max_violations }}" class="w-32 rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 mt-6 flex justify-between items-center bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                <div>
                    <p class="font-bold text-blue-900">Publish Quiz?</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ $quiz->is_published ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="pt-4 text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
