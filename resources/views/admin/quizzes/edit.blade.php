@extends('layouts.batu')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-indigo-600 font-bold text-sm transition group">
            <i class="fas fa-arrow-left transition group-hover:-translate-x-1"></i> Back to Quizzes
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 p-6 sm:p-10 md:p-12 border border-gray-100">
        <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gray-50">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                <i class="fas fa-cog"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Edit: {{ $quiz->title }}</h1>
        </div>

        <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST" class="space-y-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Quiz Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ $quiz->title }}" required class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-4 font-bold text-gray-800 focus:border-indigo-500 focus:ring-indigo-200 transition">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-4 font-bold text-gray-800 focus:border-indigo-500 focus:ring-indigo-200 transition">{{ $quiz->description }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Duration (Minutes) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="far fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="number" name="duration_minutes" required min="1" value="{{ $quiz->duration_minutes }}" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-4 pl-12 font-black text-gray-800 focus:border-indigo-500 focus:ring-indigo-200 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Total Marks <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-star absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="number" name="total_marks" required min="0" value="{{ $quiz->total_marks }}" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-4 pl-12 font-black text-gray-800 focus:border-indigo-500 focus:ring-indigo-200 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Available From</label>
                    <input type="datetime-local" name="start_at" value="{{ $quiz->start_at ? $quiz->start_at->format('Y-m-d\TH:i') : '' }}" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-4 font-bold text-gray-800 focus:border-indigo-500 focus:ring-indigo-200 transition">
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Available Until</label>
                    <input type="datetime-local" name="end_at" value="{{ $quiz->end_at ? $quiz->end_at->format('Y-m-d\TH:i') : '' }}" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 p-4 font-bold text-gray-800 focus:border-indigo-500 focus:ring-indigo-200 transition">
                </div>
            </div>

            <!-- Audience -->
            <div class="pt-8 border-t border-gray-50">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1.5 h-6 bg-indigo-600 rounded-full"></div>
                    <h2 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em]">Audience Selection</h2>
                </div>
                @php
                    $rawRoles = is_string($quiz->targeted_roles) ? json_decode($quiz->targeted_roles, true) : $quiz->targeted_roles;
                    $rolesArr = $rawRoles ?? ['all'];
                    $rolesList = [
                        'tech:software' => 'Software Team Only',
                        'tech:hardware' => 'Hardware Team Only',
                        'role:sub_leader' => 'All Sub-Leaders',
                        'tech:software|role:sub_leader' => 'Software Sub-Leaders',
                        'tech:hardware|role:sub_leader' => 'Hardware Sub-Leaders',
                        'role:vice_leader' => 'All Vice Leaders',
                        'tech:software|role:vice_leader' => 'Software Vice Leaders',
                        'tech:hardware|role:vice_leader' => 'Hardware Vice Leaders'
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/50 p-6 sm:p-8 rounded-[2rem] border border-gray-100 max-h-80 overflow-y-auto">
                    <label class="flex items-center gap-4 cursor-pointer p-4 bg-white rounded-2xl border-2 border-transparent hover:border-indigo-100 transition shadow-sm group">
                        <input type="checkbox" name="targeted_roles[]" value="all" {{ in_array('all', $rolesArr) ? 'checked' : '' }} class="w-6 h-6 text-indigo-600 rounded-lg border-gray-200 focus:ring-indigo-500 targeted-role-all">
                        <span class="font-black text-gray-800 text-sm">All Team Members</span>
                    </label>
                    @foreach($rolesList as $val => $label)
                        <label class="flex items-center gap-4 cursor-pointer p-4 bg-white rounded-2xl border-2 border-transparent hover:border-indigo-100 transition shadow-sm group">
                            <input type="checkbox" name="targeted_roles[]" value="{{ $val }}" {{ in_array($val, $rolesArr) ? 'checked' : '' }} class="w-6 h-6 text-indigo-600 rounded-lg border-gray-200 focus:ring-indigo-500 targeted-role-spec">
                            <span class="font-bold text-gray-700 text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Behavior -->
            <div class="pt-8 border-t border-gray-50">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1.5 h-6 bg-orange-500 rounded-full"></div>
                    <h2 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em]">Exam Behavior & Security</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="flex flex-col h-full bg-white rounded-[2rem] border-2 border-gray-100 p-6 hover:border-orange-200 transition cursor-pointer group shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center">
                                    <i class="fas fa-expand"></i>
                                </div>
                                <input type="checkbox" name="require_fullscreen" value="1" {{ $quiz->require_fullscreen ? 'checked' : '' }} class="w-6 h-6 text-orange-500 rounded-lg border-gray-200 focus:ring-orange-400">
                            </div>
                            <span class="font-black text-gray-800 text-sm block mb-1">Require Fullscreen</span>
                            <span class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase">Strict Enforcement</span>
                        </label>
                    </div>

                    <div class="md:col-span-1">
                        <label class="flex flex-col h-full bg-white rounded-[2rem] border-2 border-gray-100 p-6 hover:border-purple-200 transition cursor-pointer group shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                    <i class="fas fa-random"></i>
                                </div>
                                <input type="checkbox" name="shuffle_questions" value="1" {{ $quiz->shuffle_questions ? 'checked' : '' }} class="w-6 h-6 text-purple-600 rounded-lg border-gray-200 focus:ring-purple-400">
                            </div>
                            <span class="font-black text-gray-800 text-sm block mb-1">Shuffle Questions</span>
                            <span class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase">Unique Order</span>
                        </label>
                    </div>

                    <div class="md:col-span-1">
                        <label class="flex flex-col h-full bg-white rounded-[2rem] border-2 border-gray-100 p-6 hover:border-purple-200 transition cursor-pointer group shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <input type="checkbox" name="shuffle_options" value="1" {{ $quiz->shuffle_options ? 'checked' : '' }} class="w-6 h-6 text-purple-600 rounded-lg border-gray-200 focus:ring-purple-400">
                            </div>
                            <span class="font-black text-gray-800 text-sm block mb-1">Shuffle Options</span>
                            <span class="text-[10px] font-bold text-gray-400 leading-relaxed uppercase">Random Choices</span>
                        </label>
                    </div>

                    <div class="md:col-span-3 bg-red-50/50 p-8 rounded-[2rem] border border-red-100">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                            <div class="space-y-4">
                                <label class="flex items-center gap-4 cursor-pointer group">
                                    <input type="checkbox" name="auto_cancel_on_copy" value="1" {{ $quiz->auto_cancel_on_copy ? 'checked' : '' }} class="w-6 h-6 text-red-600 rounded-lg border-gray-300">
                                    <span class="font-black text-gray-800 text-sm block">Auto-DQ on Copy</span>
                                </label>
                                <label class="flex items-center gap-4 cursor-pointer group">
                                    <input type="checkbox" name="auto_cancel_on_paste" value="1" {{ $quiz->auto_cancel_on_paste ? 'checked' : '' }} class="w-6 h-6 text-red-600 rounded-lg border-gray-300">
                                    <span class="font-black text-gray-800 text-sm block">Auto-DQ on Paste</span>
                                </label>
                            </div>
                            <div class="p-6 bg-white rounded-2xl shadow-sm border border-red-50 flex-1 lg:max-w-xs">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Blur Violations Limit</label>
                                <input type="number" name="max_violations" min="1" value="{{ $quiz->max_violations }}" class="w-full rounded-xl border-gray-100 bg-gray-50/50 p-3 font-black text-red-600 focus:border-red-500 focus:ring-red-100 transition">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-10 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-4 bg-indigo-50/50 p-5 rounded-2xl border border-indigo-100 flex-1 w-full">
                    <div class="flex-1">
                        <p class="font-black text-indigo-900 text-sm">Publish Status</p>
                        <p class="text-[10px] font-bold text-indigo-400 uppercase">Current status: {{ $quiz->is_published ? 'Visible' : 'Hidden' }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ $quiz->is_published ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 shadow-inner"></div>
                    </label>
                </div>
                
                <button type="submit" class="w-full sm:w-auto px-12 py-5 bg-indigo-600 text-white rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-2xl hover:bg-indigo-700 transition transform active:scale-95 flex items-center justify-center gap-3">
                    <i class="fas fa-save text-base"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
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
@endsection
