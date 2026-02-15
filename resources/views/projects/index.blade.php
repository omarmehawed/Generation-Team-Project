@extends('layouts.batu')

@section('content')

    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 py-6">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800">Projects Dashboard</h1>
                <p class="text-gray-500 mt-1 text-sm">Manage your academic projects & collaborations.</p>
            </div>

            
        </div>

        <!-- 1. Final Project Card (ثابت لكل الطلبة) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10 relative group">
            <div class="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-yellow-400 to-yellow-600"></div>

            <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-start gap-4">
                    <div class="bg-yellow-50 p-4 rounded-xl">
                        <i class="fas fa-graduation-cap text-4xl text-yellow-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Graduation Project 2025</h2>
                        <p class="text-gray-500 max-w-xl leading-relaxed">
                            Start your final year journey here. Form your team, submit your proposal to <span
                                class="font-semibold text-gray-700">Dr. Osama El-Nahas</span>, and track your milestones.
                        </p>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="flex w-3 h-3 bg-gray-300 rounded-full"></span>
                            <span class="text-sm text-gray-500 font-medium">Status: Not Registered</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 w-full md:w-auto">
                    <a href="{{ route('final_project.start') }}" 
                    class="bg-gray-900 hover:bg-black text-white font-medium rounded-lg px-6 py-3 text-center shadow-md transition-colors">
                    Start Project
                    </a>
                    <a href="#"
                        class="text-gray-600 hover:text-gray-900 font-medium text-center text-sm underline decoration-gray-300 underline-offset-4">
                        Download Guidelines
                    </a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="flex items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-layer-group text-[#266963]"></i>
                Subject Projects
            </h3>
            <div class="flex-1 h-px bg-gray-200 ml-4"></div>
        </div>

        <!-- 2. Subject Projects Grid (ديناميكي - بيجيب من الداتا بيز) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @forelse($courses as $course)
                @php
                    // بنجيب أول مشروع للمادة دي (لو موجود)
                    $project = $course->projects->first();

                    // تحديد الألوان والنصوص بناءً على الحالة
                    $statusText = $project ? 'Active' : 'No Project';
                    $statusBg = $project ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600';
                    $dotColor = $project ? 'text-green-500' : 'text-gray-400';

                    // لون المادة (لو مش موجود نخليه أزرق افتراضي)
                    $iconColor = $course->color ?? 'blue'; 
                @endphp

                <!-- بداية الكارت -->
                <div
                    class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 cursor-pointer group relative">

                    <div class="flex justify-between items-start mb-4">
                        <!-- أيقونة المادة -->
                        <div
                            class="w-12 h-12 rounded-lg bg-{{ $iconColor }}-50 flex items-center justify-center text-{{ $iconColor }}-600 group-hover:bg-{{ $iconColor }}-600 group-hover:text-white transition-colors">
                            <i class="{{ $course->icon_class ?? 'fas fa-book' }} text-xl"></i>
                        </div>
                        <!-- حالة المشروع -->
                        <span class="{{ $statusBg }} text-xs font-bold px-3 py-1 rounded-full">
                            <i class="fas fa-circle {{ $dotColor }} text-[8px] mr-1"></i> {{ $statusText }}
                        </span>
                    </div>

                    <!-- اسم المادة -->
                    <h4 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-{{ $iconColor }}-600 transition-colors">
                        {{ $course->name }}
                    </h4>

                    <!-- تفاصيل المشروع -->
                    <p class="text-gray-500 text-sm mb-6 line-clamp-2 min-h-[40px]">
                        {{ $project ? $project->title : 'No project requested for this subject yet.' }}
                    </p>

                    <!-- الفوتر (الكود + الزرار) -->
                    <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ $course->code }}</span>

                        @if($project)
                            <a href="{{ route('projects.show', $project->id) }}"
                                class="text-sm text-blue-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                                View Details <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <span class="text-sm text-gray-300 font-medium cursor-not-allowed">
                                Not Available
                            </span>
                        @endif
                    </div>
                </div>
                <!-- نهاية الكارت -->

            @empty
                <!-- لو مفيش مواد للطالب ده -->
                <div
                    class="col-span-3 flex flex-col items-center justify-center py-12 bg-white rounded-xl border border-dashed border-gray-300 text-gray-500">
                    <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                    <p>No courses found for your account.</p>
                </div>
            @endforelse

        </div>
    </div>

@endsection