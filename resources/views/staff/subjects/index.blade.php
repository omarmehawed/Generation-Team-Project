@extends('layouts.staff')

@section('content')
    {{-- ✨ STYLES --}}
    <style>
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in-up">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-gray-800 dark:text-gray-200 flex items-center gap-3">
                    <span class="p-3 bg-blue-100 rounded-2xl text-blue-600 shadow-sm"><i
                            class="fas fa-layer-group"></i></span>
                    My Subject Projects
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2 ml-1">Manage projects for your registered courses.</p>
            </div>
        </div>
        {{-- ⚙️ لوحة التحكم الأكاديمي --}}
        @php
            $currentTerm = \App\Models\Setting::where('key', 'current_term')->value('value') ?? 1;
        @endphp

        {{-- 🔒 شرط الحماية: يظهر فقط لمن يملك صلاحية التحكم الأكاديمي --}}
        @if (auth()->user()->hasPermission('manage_academic_control'))
            <div
                class="bg-gray-900 rounded-[2rem] p-6 mb-10 text-white shadow-xl flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
                {{-- خلفية جمالية --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-purple-600 rounded-full blur-3xl opacity-20 -mr-16 -mt-16">
                </div>

                <div class="relative z-10">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i class="fas fa-cogs text-purple-400"></i> Academic Control
                    </h2>
                    <p class="text-gray-400 text-sm mt-1">
                        Current Status: <span class="text-purple-400 font-bold">Term {{ $currentTerm ?? 1 }}</span>
                    </p>
                </div>

                <div class="flex gap-3 relative z-10">
                    {{-- زرار الترم الأول --}}
                    <form action="{{ route('staff.system.change_term') }}" method="POST">
                        @csrf <input type="hidden" name="term" value="1">
                        <button type="submit"
                            class="px-5 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2
                            {{ ($currentTerm ?? 1) == 1 ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-white text-gray-900 hover:bg-purple-50' }}"
                            {{ ($currentTerm ?? 1) == 1 ? 'disabled' : '' }}>
                            Term 1
                        </button>
                    </form>

                    {{-- زرار الترم الثاني --}}
                    <form action="{{ route('staff.system.change_term') }}" method="POST">
                        @csrf <input type="hidden" name="term" value="2">
                        <button type="submit"
                            class="px-5 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2
                            {{ ($currentTerm ?? 1) == 2 ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-white text-gray-900 hover:bg-purple-50' }}"
                            {{ ($currentTerm ?? 1) == 2 ? 'disabled' : '' }}>
                            Term 2
                        </button>
                    </form>

                    {{-- زرار نهاية العام (خطير) --}}
                    <div class="w-px bg-gray-700 mx-2"></div>

                    <form action="{{ route('staff.system.promote') }}" method="POST"
                        onsubmit="return confirmFormSubmit(event, this, '⚠️ تحذير: هل أنت متأكد؟\nسيتم نقل جميع الطلاب للسنة التالية وإعادة النظام للترم الأول.');">
                        @csrf
                        <button type="submit"
                            class="px-5 py-2 rounded-xl text-sm font-bold bg-red-600 hover:bg-red-700 text-white transition flex items-center gap-2">
                            <i class="fas fa-level-up-alt"></i> End Year
                        </button>
                    </form>
                </div>
            </div>
        @endif
        {{-- 🔒 نهاية الشرط --}}
        {{-- 📚 GRID: كروت المواد --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            {{-- تكرار المواد (Loop) --}}
            {{-- تكرار المواد (Loop) --}}
            @forelse($courses as $course)
                @php
                    // 🛠️ هنا الحل: بنجيب المشروع ونحطه في متغير عشان نفحصه الأول
                    $project = $course->projects->first();
                @endphp

                <a href="{{ route('subjects.manage', $course->id) }}" class="group block">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-lg p-6 relative overflow-hidden hover-lift h-full">
                        {{-- زخرفة خلفية --}}
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-{{ $course->color ??'blue' }}-50 rounded-full blur-3xl -mr-10 -mt-10 opacity-60 transition group-hover:opacity-100">
                        </div>

                        <div class="relative z-10">
                            {{-- الأيقونة والكود --}}
                            <div class="flex justify-between items-start mb-6">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-{{ $course->color ??'blue' }}-100 text-{{ $course->color ?? 'blue' }}-600 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                                    <i class="{{ $course->icon_class ??'fas fa-book' }}"></i>
                                </div>
                                <span
                                    class="bg-gray-900 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-md">
                                    {{ $course->code }}
                                </span>
                            </div>

                            {{-- التفاصيل --}}
                            <h3 class="text-xl font-black text-gray-800 dark:text-gray-200 mb-2 group-hover:text-blue-600 transition-colors">
                                {{ $course->name }}
                            </h3>
                            <p class="text-sm text-gray-400 mb-6 line-clamp-2">
                                {{ $project ? $project->description : 'No project active yet.' }}
                            </p>

                            {{-- إحصائيات سريعة (مؤمنة ضد الأخطاء) --}}
                            <div class="flex items-center gap-4 pt-4 border-t border-gray-50">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-users text-gray-300"></i>
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                                        {{-- ✅ لو المشروع موجود عد التيمات، لو لأ اكتب 0 --}}
                                        {{ $project ? $project->teams->count() : 0 }} Teams
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-gray-300"></i>
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                                        {{-- ✅ لو المشروع موجود عد التسليمات، لو لأ اكتب 0 --}}
                                        {{ $project ? $project->teams->where('status', 'submitted')->count() : 0 }}
                                        Submitted
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                {{-- لو مفيش مواد --}}
                <div
                    class="col-span-full py-16 text-center bg-gray-50 dark:bg-gray-900 rounded-[3rem] border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="fas fa-folder-open text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-gray-800 dark:text-gray-200 font-bold text-lg">No Courses Found</h3>
                    <p class="text-gray-400 text-sm">You haven't been assigned to any subject projects yet.</p>
                </div>
            @endforelse

        </div>
    </div>
@endsection
