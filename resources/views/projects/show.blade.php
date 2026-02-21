@extends('layouts.batu')

@section('content')

    {{-- ========================================== --}}
    {{-- 🎨 1. منطقة الـ CSS (التحابيش والأنيميشن) --}}
    {{-- دي اللي بتعمل الحركات الناعمة أول ما الصفحة تفتح --}}
    {{-- ========================================== --}}
    <style>
        /* =========================================
                                           1. شاشة التحميل (Preloader) - مقتبس من Graduation
                                           ========================================= */
        #royal-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0f0f0f;
            /* لون الخلفية الغامق */
            z-index: 99999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        /* الدائرة الذهبية */
        .loader-spinner {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(212, 175, 55, 0.3);
            border-radius: 50%;
            border-top-color: #D4AF37;
            animation: spin 1s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.5);
        }

        /* حركة الدوران */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* النص الذهبي اللامع */
        .text-gold-gradient {
            background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: shimmer 3s linear infinite;
            font-weight: 900;
            text-shadow: 0 0 10px rgba(191, 149, 63, 0.3);
            text-align: center;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        /* =========================================
                                           2. الأنيميشن المحسن (Updated Animations)
                                           ========================================= */

        /* تحديث حركة الظهور لتكون أنعم زي الجراديواشن */
        .fade-in-up {
            animation: fadeInUp 1s cubic-bezier(0.2, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(40px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* تأخير زمني للعناصر */
        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        /* =========================================
                                           3. كلاسات Subject Project الأصلية (تم الاحتفاظ بها)
                                           ========================================= */

        /* تأثير الزجاج (Glass Effect) */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* تكبير الكارت لما الماوس يجي عليه */
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-scale:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        /* كلاس لإخفاء اللودر */
        .loaded {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
    </style>
    {{-- 🔥 شاشة التحميل (Preloader) 🔥 --}}
    <div id="royal-preloader">
        <div class="loader-spinner mb-4"></div>
        <h2 class="text-gold-gradient text-xl tracking-[0.3em] font-bold uppercase animate-pulse">
            Loading Subject Project <br>
            Devloped by Omar Mehawed
            <span style="font-size: 0.8em; opacity: 0.8;">Please Wait...</span>
        </h2>
    </div>


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen">

        {{-- 🧭 2. شريط التنقل (Breadcrumb) --}}
        {{-- ده عشان يعرف الطالب هو واقف فين في الموقع --}}
        <nav class="flex mb-6 text-gray-500 text-sm font-medium fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('projects.index') }}"
                        class="hover:text-[#266963] transition-colors flex items-center gap-2">
                        <div class="p-1 bg-white rounded-md shadow-sm"><i class="fas fa-home"></i></div> Projects
                    </a>
                </li>
                <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
                <li>
                    {{-- اسم المادة --}}
                    <span
                        class="text-[#266963] bg-green-50 px-3 py-1 rounded-full text-xs font-bold border border-green-100">{{ $project->course->name }}</span>
                </li>
            </ol>
        </nav>

        {{-- 👑 3. الهيدر الفخم (Hero Section) --}}
        {{-- ده واخد نفس ألوان صاحبك (الأخضر الغامق المتدرج) --}}
        <div
            class="relative bg-gradient-to-br from-[#1A4D48] to-[#266963] rounded-[2rem] shadow-xl overflow-hidden mb-10 text-white fade-in-up delay-100">
            {{-- شوية دوائر وزخارف في الخلفية --}}
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 left-0 w-40 h-40 bg-yellow-400 opacity-10 rounded-full blur-2xl -ml-10 -mb-10 pointer-events-none">
            </div>

            <div class="relative z-10 p-8 md:p-10 flex flex-col md:flex-row justify-between items-start gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        {{-- كود المادة --}}
                        <span
                            class="bg-white/20 backdrop-blur-md border border-white/20 text-white text-[10px] font-black px-3 py-1 rounded-lg uppercase tracking-widest shadow-sm">
                            {{ $project->course->code }}
                        </span>
                        {{-- الديدلاين --}}
                        <span
                            class="text-green-100 text-sm flex items-center gap-1 bg-black/20 px-3 py-1 rounded-lg border border-white/10">
                            <i class="far fa-clock text-yellow-300 animate-pulse"></i> Deadline: <span
                                class="font-bold">{{ $project->deadline ?? 'TBA' }}</span>
                        </span>
                    </div>

                    <h1 class="text-4xl font-extrabold tracking-tight mb-2 drop-shadow-sm">{{ $project->title }}</h1>
                    <p
                        class="text-green-100 max-w-2xl text-sm leading-relaxed opacity-90 border-l-2 border-yellow-400 pl-3">
                        {{ $project->description ?? 'Collaborate with your team, manage tasks efficiently, and submit your best work.' }}
                    </p>
                </div>

                {{-- حالة الطالب (منضم ولا لا) --}}
                @if($myTeam)
                    <div
                        class="bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl text-center min-w-[140px] shadow-lg transform rotate-2 hover:rotate-0 transition-transform">
                        <p class="text-[10px] text-green-200 font-bold uppercase tracking-wider mb-1">Your Status</p>
                        <div class="flex items-center justify-center gap-2 text-xl font-bold text-white">
                            <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse shadow-[0_0_10px_#4ade80]"></div>
                            Joined
                        </div>
                    </div>
                @else
                    <div
                        class="bg-white/10 backdrop-blur-md border p-4 rounded-2xl text-center min-w-[140px] shadow-lg border-red-400/30">
                        <p class="text-[10px] text-red-200 font-bold uppercase tracking-wider mb-1">Your Status</p>
                        <div class="flex items-center justify-center gap-2 text-xl font-bold text-white">
                            <i class="fas fa-exclamation-circle text-red-300"></i> No Team
                        </div>
                    </div>
                @endif
            </div>

            {{-- 📊 4. شريط الإحصائيات (Stats Bar) --}}
            {{-- بيظهر بس لو الطالب جوه تيم، بيحسب النسبة المئوية --}}
            @if($myTeam)
                @php
                    $totalTasks = $myTeam->tasks->count();
                    $completedTasks = $myTeam->tasks->where('status', 'completed')->count();
                    // معادلة حساب النسبة عشان ميطلعش إيرور لو مفيش تاسكات
                    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                @endphp
                <div
                    class="bg-black/20 backdrop-blur-sm border-t border-white/10 px-8 py-4 flex flex-wrap gap-8 items-center text-sm">
                    {{-- البروجريس بار --}}
                    <div class="flex-1 min-w-[200px]">
                        <div class="flex justify-between text-xs text-green-100 mb-1">
                            <span>Project Progress</span>
                            <span class="font-bold">{{ $progress }}%</span>
                        </div>
                        <div class="w-full h-2 bg-black/30 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-yellow-400 to-green-400 transition-all duration-1000"
                                style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                    {{-- عداد التاسكات --}}
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center"><i
                                class="fas fa-tasks text-blue-300"></i></div>
                        <div>
                            <p class="text-green-200 text-[10px] uppercase">Tasks Done</p>
                            <p class="font-bold">{{ $completedTasks }} <span class="text-white/50">/ {{ $totalTasks }}</span>
                            </p>
                        </div>
                    </div>
                    {{-- عداد الأعضاء --}}
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center"><i
                                class="fas fa-users text-purple-300"></i></div>
                        <div>
                            <p class="text-green-200 text-[10px] uppercase">Members</p>
                            <p class="font-bold">{{ $myTeam->members->count() }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ========================================== --}}
        {{-- 🔄 5. المنطق (Logic) - لو في تيم أو مفيش --}}
        {{-- ========================================== --}}

        @if(!$myTeam)
            {{-- 🅰️ الحالة الأولى: مفيش تيم (عرض الكروت الكبيرة) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 fade-in-up delay-200">

                {{-- كارت الإنشاء --}}
                <div
                    class="bg-white p-10 rounded-[2rem] border border-gray-100 shadow-xl hover-scale group cursor-pointer relative overflow-hidden text-center">
                    <div
                        class="absolute top-0 right-0 bg-[#266963]/5 w-40 h-40 rounded-bl-full -mr-0 -mt-0 transition-all group-hover:bg-[#266963]/10">
                    </div>

                    <div
                        class="w-24 h-24 bg-gradient-to-br from-[#266963] to-[#1e524d] rounded-2xl rotate-3 flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:rotate-6 transition-transform duration-300">
                        <i class="fas fa-rocket text-4xl text-white"></i>
                    </div>

                    <h3 class="text-2xl font-extrabold text-gray-800 mb-2">Create New Team</h3>
                    <p class="text-gray-500 mb-8 text-sm">Lead the way! Form your squad and start the journey.</p>

                    {{-- زرار يفتح مودال الإنشاء --}}
                    <button onclick="openModal('createTeamModal')"
                        class="w-full bg-[#266963] hover:bg-[#1A4D48] text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-[#266963]/30 transition-all flex items-center justify-center gap-2">
                        <span>Start Leadership</span> <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                {{-- كارت الانضمام --}}
                <div
                    class="bg-white p-10 rounded-[2rem] border border-gray-100 shadow-xl hover-scale group cursor-pointer relative overflow-hidden text-center">
                    <div
                        class="absolute top-0 left-0 bg-blue-50 w-40 h-40 rounded-br-full -ml-0 -mt-0 transition-all group-hover:bg-blue-100">
                    </div>

                    <div
                        class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl -rotate-3 flex items-center justify-center mx-auto mb-6 shadow-xl group-hover:-rotate-0 transition-transform duration-300">
                        <i class="fas fa-handshake text-4xl text-white"></i>
                    </div>

                    <h3 class="text-2xl font-extrabold text-gray-800 mb-2">Join Existing Team</h3>
                    <p class="text-gray-500 mb-8 text-sm">Have a code? Enter it and collaborate instantly.</p>

                    {{-- زرار يفتح مودال الانضمام --}}
                    <button onclick="openModal('joinTeamModal')"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                        <span>Enter Code</span> <i class="fas fa-key"></i>
                    </button>
                </div>
            </div>

        @else
            {{-- 🅱️ الحالة الثانية: الطالب في تيم (الداشبورد الكاملة) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in-up delay-200">

                {{-- ⬅️ العمود الجانبي --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- كارت معلومات التيم والكود --}}
                    <div
                        class="bg-white rounded-[2rem] border border-gray-100 shadow-lg p-6 text-center relative overflow-hidden group">
                        {{-- خط ملون فوق --}}
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#266963] to-green-400"></div>

                        <div
                            class="w-24 h-24 bg-gradient-to-br from-[#266963] to-green-600 rounded-full p-1 mx-auto mb-4 shadow-xl group-hover:scale-105 transition-transform">
                            <div class="w-full h-full bg-white rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-4xl text-[#266963]"></i>
                            </div>
                        </div>
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-1">{{ $myTeam->name }}</h2>
                        <span class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Team Name</span>

                        <div
                            class="bg-gray-50 rounded-2xl p-4 border-2 border-dashed border-gray-200 mt-6 group hover:border-[#266963] transition-colors relative">
                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Access Code</p>
                            <div class="flex items-center justify-center gap-3">
                                <span
                                    class="text-3xl font-mono font-bold text-gray-800 tracking-widest select-all">{{ $myTeam->code }}</span>
                                <button onclick="navigator.clipboard.writeText('{{ $myTeam->code }}'); alert('Code Copied!')"
                                    class="text-gray-400 hover:text-[#266963] transition-colors p-2 rounded-full hover:bg-white hover:shadow-sm">
                                    <i class="far fa-copy text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- كارت دوري في التيم (My Role) --}}
                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-lg p-6">
                        <h3 class="text-xs font-bold text-gray-400 uppercase mb-4 tracking-wider border-b pb-2">My Role &
                            Actions</h3>
                        @php $myRole = $myTeam->members->where('user_id', auth()->id())->first()->role; @endphp

                        <div class="flex justify-between items-center mb-6 bg-gray-50 p-3 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center {{ $myRole == 'leader' ? 'bg-yellow-100 text-yellow-600' : 'bg-blue-100 text-blue-600' }}">
                                    <i class="fas {{ $myRole == 'leader' ? 'fa-crown' : 'fa-user' }}"></i>
                                </div>
                                <span class="font-bold text-gray-700 capitalize text-sm">{{ ucfirst($myRole) }}</span>
                            </div>
                            @if($myRole == 'leader')
                                <span
                                    class="text-[9px] bg-yellow-500 text-white px-2 py-0.5 rounded-full shadow-sm font-bold">ADMIN</span>
                            @endif
                        </div>

                        {{-- زر الخروج من التيم --}}
                        <button onclick="openModal('leaveTeamModal')"
                            class="w-full border-2 border-red-100 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 font-bold rounded-xl text-sm px-4 py-3 transition-all flex items-center justify-center gap-2 group">
                            <i class="fas fa-sign-out-alt group-hover:-translate-x-1 transition-transform"></i> Leave Team
                        </button>
                    </div>

                    {{-- كارت ميعاد المناقشة (تم التصحيح لـ $myTeam) --}}
                    @if(isset($myTeam) && $myTeam)
                        <div class="mt-6">
                            @if($myTeam->defense_date)
                                {{-- ✅ تم تحديد الميعاد --}}
                                <div
                                    class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#6366f1] to-[#8b5cf6] shadow-xl shadow-indigo-500/20 text-white p-6 transform transition-all duration-300 hover:-translate-y-1 group">

                                    {{-- خلفية ديكور --}}
                                    <div
                                        class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10 blur-2xl group-hover:opacity-20 transition">
                                    </div>

                                    <div class="relative z-10 text-center">
                                        <div
                                            class="inline-block bg-white/20 backdrop-blur-md border border-white/20 rounded-full px-3 py-1 text-[10px] font-bold mb-3 shadow-sm">
                                            <i class="fas fa-check-circle text-green-300"></i> Confirmed
                                        </div>

                                        <h3 class="text-xl font-black mb-1">Defense Day 🎓</h3>
                                        <p class="text-indigo-100 text-xs mb-4">Get ready to impress!</p>

                                        {{-- التاريخ والوقت --}}
                                        <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10">
                                            <div class="text-3xl font-black">
                                                {{ \Carbon\Carbon::parse($myTeam->defense_date)->format('d') }}
                                                <span
                                                    class="text-sm font-bold uppercase">{{ \Carbon\Carbon::parse($myTeam->defense_date)->format('M') }}</span>
                                            </div>
                                            <div class="h-px bg-white/20 my-2"></div>
                                            <div class="flex justify-between items-center text-xs font-bold">
                                                <span class="flex items-center gap-1"><i class="far fa-clock text-yellow-300"></i>
                                                    {{ \Carbon\Carbon::parse($myTeam->defense_date)->format('h:i A') }}</span>
                                                <span class="flex items-center gap-1 opacity-80"><i class="fas fa-map-marker-alt"></i>
                                                    {{ $myTeam->defense_location ?? 'TBD' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- ⏳ لم يتم التحديد --}}
                                <div
                                    class="rounded-[2rem] border-2 border-dashed border-gray-200 bg-white p-6 text-center group hover:border-indigo-300 hover:bg-indigo-50/30 transition-all duration-300 mt-6">
                                    <div
                                        class="w-12 h-12 bg-gray-50 rounded-xl shadow-sm flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-hourglass-half text-indigo-300 text-xl animate-pulse"></i>
                                    </div>
                                    <h4 class="text-gray-800 font-bold text-sm">Schedule Pending</h4>
                                    <p class="text-gray-400 text-[10px] mt-1">
                                        Defense date not set yet.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- ➡️ العمود الرئيسي (الأعضاء والتاسكات) --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- 6. جدول الأعضاء --}}
                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-lg overflow-hidden hover-scale">
                        <div
                            class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 backdrop-blur-sm">
                            <h3 class="font-bold text-gray-800 flex items-center gap-3">
                                <div class="p-2 bg-[#266963]/10 rounded-lg text-[#266963]"><i class="fas fa-users-cog"></i>
                                </div>
                                Team Members
                                <span
                                    class="bg-[#266963] text-white text-xs py-0.5 px-2.5 rounded-full shadow-sm">{{ $myTeam->members->count() }}</span>
                            </h3>
                            @if($myRole == 'leader')
                                <button onclick="openModal('inviteMemberModal')"
                                    class="text-xs bg-[#266963] hover:bg-[#1A4D48] text-white px-4 py-2 rounded-xl transition shadow-md flex items-center gap-2 transform hover:-translate-y-0.5 font-bold">
                                    <i class="fas fa-user-plus"></i> Invite
                                </button>
                            @endif
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-400 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-8 py-4">Student</th>
                                        <th class="px-8 py-4">Role</th>
                                        <th class="px-8 py-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($myTeam->members as $member)
                                        <tr class="bg-white hover:bg-gray-50/80 transition-colors">
                                            <td
                                                class="px-8 py-4 font-medium text-gray-900 whitespace-nowrap flex items-center gap-4">
                                                <img class="w-10 h-10 rounded-full border-2 border-white shadow-sm"
                                                    src="https://ui-avatars.com/api/?name={{ $member->user->name }}&background=random&color=fff">
                                                <div>
                                                    <div class="text-sm font-bold">{{ $member->user->name }}</div>
                                                    <div class="text-xs text-gray-400 font-normal">{{ $member->user->email }}</div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-4">
                                                @if($member->role == 'leader')
                                                    <span
                                                        class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200">
                                                        <i class="fas fa-crown text-[8px]"></i> Leader
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                                                        Member
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-8 py-4 text-right">
                                                <div
                                                    class="flex items-center justify-end gap-2 opacity-60 hover:opacity-100 transition-opacity">
                                                    @if($myRole == 'leader' && $member->role != 'leader')
                                                        <form action="{{ route('teams.removeMember') }}" method="POST"
                                                            class="inline-block" onsubmit="return confirm('Remove this member?')">
                                                            @csrf
                                                            <input type="hidden" name="team_id" value="{{ $myTeam->id }}">
                                                            <input type="hidden" name="user_id" value="{{ $member->user_id }}">
                                                            <button
                                                                class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center justify-center shadow-sm"
                                                                title="Remove">
                                                                <i class="fas fa-trash-alt text-xs"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($member->user_id != auth()->id())
                                                        <button
                                                            onclick="openReportModal('{{ $member->user_id }}', '{{ $member->user->name }}')"
                                                            class="w-8 h-8 rounded-full bg-gray-50 text-gray-400 hover:bg-gray-800 hover:text-white transition flex items-center justify-center shadow-sm"
                                                            title="Report">
                                                            <i class="fas fa-flag text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 7. قسم التاسكات --}}
                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-lg overflow-hidden hover-scale">
                        <div
                            class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 backdrop-blur-sm">
                            <h3 class="font-bold text-gray-800 flex items-center gap-3">
                                <div class="p-2 bg-orange-100 rounded-lg text-orange-500"><i class="fas fa-tasks"></i></div>
                                Project Tasks
                            </h3>
                        </div>

                        <div class="p-8">
                            {{-- فورم إضافة تاسك (لو أنا ليدر) --}}
                            @if($myRole == 'leader')
                                <form action="{{ route('tasks.store') }}" method="POST" class="group relative mb-8">
                                    <div
                                        class="absolute -inset-0.5 bg-gradient-to-r from-[#266963] to-green-400 rounded-2xl opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200 animate-tilt">
                                    </div>
                                    <div
                                        class="relative flex flex-col md:flex-row gap-3 bg-white p-4 rounded-xl items-center border border-gray-100">
                                        @csrf
                                        <input type="hidden" name="team_id" value="{{ $myTeam->id }}">
                                        <div class="flex-1 w-full">
                                            <input type="text" name="title" placeholder="✍️ Add a new task..." required
                                                class="w-full bg-gray-50 border-0 rounded-xl text-gray-900 text-sm focus:ring-2 focus:ring-[#266963] transition-all p-3 shadow-inner">
                                        </div>
                                        <div class="flex gap-2 w-full md:w-auto">
                                            <input type="date" name="deadline"
                                                class="bg-gray-50 border-0 rounded-xl text-sm text-gray-600 focus:ring-[#266963]">
                                            <select name="user_id"
                                                class="bg-gray-50 border-0 rounded-xl text-sm focus:ring-[#266963] w-32">
                                                @foreach($myTeam->members as $member)
                                                    <option value="{{ $member->user->id }}">
                                                        {{ \Illuminate\Support\Str::limit($member->user->name, 10) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                class="bg-[#266963] hover:bg-[#1A4D48] text-white px-4 rounded-xl font-bold shadow-md transition-transform hover:scale-105">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif

                            {{-- قائمة التاسكات (التكرار) --}}
                            <div class="space-y-4">
                                @forelse($myTeam->tasks as $task)
                                                @php
                                                    // حساب لو التاسك متأخر
                                                    $isOverdue = $task->deadline && \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status != 'completed' && $task->status != 'reviewing';
                                                @endphp

                                                <div
                                                    class="group relative flex flex-col sm:flex-row sm:items-center justify-between p-5 rounded-2xl border transition-all duration-300 hover:-translate-y-1 hover:shadow-lg
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $task->status == 'completed' ? 'bg-green-50/50 border-green-100' :
                                    ($task->status == 'reviewing' ? 'bg-yellow-50/50 border-yellow-100' :
                                        ($task->status == 'rejected' || $isOverdue ? 'bg-red-50/50 border-red-100' : 'bg-white border-gray-100')) }}">

                                                    <div class="flex items-start gap-5 mb-4 sm:mb-0">
                                                        {{-- أيقونة الحالة --}}
                                                        <div class="mt-1">
                                                            @if($task->status == 'completed')
                                                                <div
                                                                    class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center shadow-lg shadow-green-500/30">
                                                                    <i class="fas fa-check"></i>
                                                                </div>
                                                            @elseif($task->status == 'reviewing')
                                                                <div
                                                                    class="w-8 h-8 rounded-full bg-yellow-400 text-white flex items-center justify-center shadow-lg shadow-yellow-400/30 animate-pulse">
                                                                    <i class="fas fa-clock"></i>
                                                                </div>
                                                            @elseif($isOverdue)
                                                                <div
                                                                    class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center shadow-lg shadow-red-500/30 animate-bounce">
                                                                    <i class="fas fa-exclamation"></i>
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="w-8 h-8 rounded-full border-2 border-gray-200 text-gray-300 flex items-center justify-center">
                                                                    <i class="fas fa-circle text-[8px]"></i>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div>
                                                            <p class="font-bold text-gray-800 text-base flex items-center gap-2">
                                                                {{ $task->title }}
                                                                @if($isOverdue) <span
                                                                    class="bg-red-100 text-red-600 text-[9px] px-2 py-0.5 rounded-md font-black tracking-wider border border-red-200">OVERDUE</span>
                                                                @endif
                                                            </p>
                                                            <div class="flex flex-wrap items-center gap-4 mt-2 text-xs text-gray-500">
                                                                <div
                                                                    class="flex items-center gap-1.5 bg-white px-2 py-1 rounded-md border border-gray-100 shadow-sm">
                                                                    <img class="w-4 h-4 rounded-full"
                                                                        src="https://ui-avatars.com/api/?name={{ $task->user->name }}&background=random">
                                                                    <span class="font-bold">{{ $task->user->name }}</span>
                                                                </div>
                                                                @if($task->deadline)
                                                                    <span
                                                                        class="flex items-center gap-1 {{ $isOverdue ? 'text-red-500 font-bold' : '' }}">
                                                                        <i class="far fa-clock"></i>
                                                                        {{ \Carbon\Carbon::parse($task->deadline)->format('M d') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- أزرار التحكم في التاسك --}}
                                                    <div class="flex items-center gap-3">
                                                        {{-- لو فيه ملف مرفوع --}}
                                                        @if(($task->submission_value || $task->submission_file) && ($task->status == 'reviewing' || $task->status == 'completed'))

                                                            <a href="{{ $task->submission_type == 'link' ? $task->submission_value : $task->submission_file }}"
                                                                target="_blank"
                                                                class="w-8 h-8 rounded-full bg-white border border-gray-200 text-[#266963] flex items-center justify-center hover:bg-[#266963] hover:text-white transition shadow-sm"
                                                                title="{{ $task->submission_type == 'link' ? 'Open Link' : 'Download File' }}">

                                                                {{-- تغيير الأيقونة حسب النوع --}}
                                                                <i
                                                                    class="{{ $task->submission_type == 'link' ? 'fas fa-link' : 'fas fa-file-download' }}"></i>
                                                            </a>

                                                        @endif

                                                        {{-- لو أنا ليدر والتاسك بيتراجع --}}
                                                        @if($myRole == 'leader' && $task->status == 'reviewing')
                                                            <form action="{{ route('tasks.approve', $task->id) }}" method="POST">@csrf <button
                                                                    class="w-8 h-8 rounded-full bg-green-100 text-green-600 hover:bg-green-600 hover:text-white transition flex items-center justify-center"><i
                                                                        class="fas fa-check"></i></button></form>
                                                            <form action="{{ route('tasks.reject', $task->id) }}" method="POST">@csrf <button
                                                                    class="w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition flex items-center justify-center"><i
                                                                        class="fas fa-times"></i></button></form>
                                                        @endif

                                                        {{-- زر التسليم --}}
                                                        @if((auth()->id() == $task->user_id || $myRole == 'leader') && ($task->status == 'pending' || $task->status == 'rejected'))
                                                            <button onclick="openSubmitModal('{{ $task->id }}', '{{ $task->title }}')"
                                                                class="px-4 py-2 rounded-lg text-xs font-bold text-white shadow-md transition-transform hover:-translate-y-0.5 {{ $isOverdue ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-600 hover:bg-blue-700' }}">
                                                                {{ $isOverdue ? 'Submit Late' : ($task->status == 'rejected' ? 'Resubmit' : 'Submit') }}
                                                            </button>
                                                        @endif

                                                        {{-- زر الحذف --}}
                                                        @if($myRole == 'leader')
                                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                                                onsubmit="return confirm('Delete task?')">
                                                                @csrf @method('DELETE')
                                                                <button
                                                                    class="w-8 h-8 rounded-full hover:bg-red-50 text-gray-300 hover:text-red-500 transition flex items-center justify-center"><i
                                                                        class="fas fa-trash-alt"></i></button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                @empty
                                    <div class="text-center py-12">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-clipboard-list text-gray-300 text-3xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No tasks yet.</p>
                                        <p class="text-gray-400 text-xs">Start by assigning work to your team.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- 8. كارت التسليم النهائي --}}
                    @if(isset($myTeam) && $myTeam)
                        <div
                            class="bg-white rounded-[2rem] border border-gray-100 shadow-xl p-8 relative overflow-hidden group hover-scale">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110">
                            </div>

                            <h3 class="text-sm font-extrabold text-gray-900 uppercase mb-6 flex items-center gap-2 relative z-10">
                                <span class="w-2 h-6 bg-[#266963] rounded-full"></span> Final Submission
                            </h3>

                            @if($myTeam && ($myTeam->status == 'submitted' || $myTeam->status == 'graded'))
                                <div class="text-center py-6 bg-green-50/50 rounded-2xl border border-green-100 relative z-10">
                                    <div
                                        class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg animate-bounce">
                                        <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                                    </div>
                                    <h4 class="text-green-800 font-bold text-2xl mb-1">Project Submitted!</h4>
                                    <p class="text-sm text-green-600">Great job! Your work has been sent.</p>

                                    <div class="flex justify-center gap-4 mt-6">
                                        @if(filter_var($myTeam->submission_link, FILTER_VALIDATE_URL))
                                            <a href="{{ $myTeam->submission_link }}" target="_blank"
                                                class="px-5 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-black transition shadow-lg"><i
                                                    class="fab fa-github mr-2"></i> Repo</a>
                                        @endif
                                        @if($myTeam->submission_path)
                                            <a href="{{ $myTeam->submission_path }}" target="_blank"
                                                class="px-5 py-2.5 bg-white text-[#266963] border border-[#266963] rounded-xl text-sm font-bold hover:bg-[#266963] hover:text-white transition shadow-lg"><i
                                                    class="fas fa-download mr-2"></i> File</a>
                                        @endif
                                    </div>

                                    @if($myTeam->grade)
                                        <div class="mt-8 pt-6 border-t border-dashed border-green-200">
                                            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Final Grade</p>
                                            <div class="flex justify-center items-end gap-1 mt-2">
                                                <span class="text-5xl font-black text-[#266963]">{{ $myTeam->grade }}</span>
                                                <span class="text-xl text-gray-400 font-medium mb-2">/ 100</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                @if($myRole == 'leader')
                                    <form action="{{ route('teams.submit') }}" method="POST" enctype="multipart/form-data"
                                        x-data="{ type: 'link' }">
                                        @csrf
                                        <input type="hidden" name="team_id" value="{{ $myTeam->id }}">

                                        <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 mb-6 flex gap-3 items-start">
                                            <i class="fas fa-lightbulb text-yellow-500 mt-1"></i>
                                            <p class="text-xs text-yellow-800 leading-relaxed">
                                                Make sure this is your <strong>Final Version</strong>. Once submitted, you cannot edit
                                                unless the doctor reopens it.
                                            </p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mb-6">
                                            <label class="cursor-pointer">
                                                <input type="radio" name="submission_type" value="link" class="peer hidden" x-model="type">
                                                <div
                                                    class="p-4 rounded-xl border-2 border-gray-100 text-center peer-checked:border-[#266963] peer-checked:bg-[#266963]/5 transition-all hover:border-gray-200">
                                                    <i class="fab fa-github text-2xl mb-2 text-gray-700 peer-checked:text-[#266963]"></i>
                                                    <p class="text-sm font-bold text-gray-700 peer-checked:text-[#266963]">Github Link</p>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="radio" name="submission_type" value="file" class="peer hidden" x-model="type">
                                                <div
                                                    class="p-4 rounded-xl border-2 border-gray-100 text-center peer-checked:border-[#266963] peer-checked:bg-[#266963]/5 transition-all hover:border-gray-200">
                                                    <i
                                                        class="fas fa-file-archive text-2xl mb-2 text-gray-700 peer-checked:text-[#266963]"></i>
                                                    <p class="text-sm font-bold text-gray-700 peer-checked:text-[#266963]">Upload File</p>
                                                </div>
                                            </label>
                                        </div>

                                        <div x-show="type === 'link'" x-transition class="mb-4">
                                            <input type="url" name="link" placeholder="https://github.com/username/project"
                                                class="w-full border-gray-300 rounded-xl p-3 focus:ring-[#266963] focus:border-[#266963] shadow-sm">
                                        </div>
                                        <div x-show="type === 'file'" x-transition class="mb-4">
                                            <input type="file" name="project_file"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#266963]/10 file:text-[#266963] hover:file:bg-[#266963]/20 transition cursor-pointer border rounded-xl">
                                        </div>

                                        <textarea name="comment" rows="3" placeholder="Add any notes for the doctor (Optional)..."
                                            class="w-full border-gray-300 rounded-xl p-3 focus:ring-[#266963] focus:border-[#266963] shadow-sm mb-6"></textarea>

                                        <button type="submit"
                                            class="w-full bg-gradient-to-r from-[#266963] to-[#1e524d] hover:from-[#1e524d] hover:to-[#163C38] text-white font-bold py-4 rounded-xl shadow-lg transition-all transform hover:-translate-y-1"
                                            onclick="return confirm('Confirm Final Submission?')">
                                            Submit Project <i class="fas fa-paper-plane ml-2"></i>
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                        <div class="animate-spin-slow inline-block mb-3">
                                            <i class="fas fa-circle-notch text-gray-300 text-3xl"></i>
                                        </div>
                                        <p class="text-gray-600 font-bold">Waiting for Submission</p>
                                        <p class="text-xs text-gray-400 mt-1">Only the Team Leader can submit.</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

                </div>


            </div>

        @endif


    </div>

    @include('projects.modals')

    {{-- ================================================ --}}
    {{-- 📜 الجافاسكريبت (Scripts) 📜 --}}
    {{-- نفس الكود بتاعك ماتغيرش عشان الوظائف تشتغل تمام --}}
    {{-- ================================================ --}}

    <script>
        // عند اكتمال تحميل الصفحة
        window.addEventListener('load', function () {
            const preloader = document.getElementById('royal-preloader');

            // إضافة كلاس الاختفاء
            preloader.classList.add('loaded');

            // اختياري: حذف العنصر تماماً من الصفحة بعد انتهاء الأنيميشن
            setTimeout(() => {
                preloader.remove();
            }, 500);
        });
    </script>
    <script>
        // دوال فتح وقفل المودالز العامة
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // منع السكرول للصفحة
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; // إرجاع السكرول
            }
        }

        // فتح مودال تسليم التاسك وتجهيز الرابط
        function openSubmitModal(taskId, taskTitle) {
            document.getElementById('taskModalTitle').innerHTML = '<span class="text-[#266963]">Submit:</span> ' + taskTitle;
            document.getElementById('submitTaskForm').action = '/tasks/' + taskId + '/submit';
            openModal('submitTaskModal');
        }

        // فتح مودال الريبورت
        function openReportModal(userId, userName) {
            document.getElementById('reportedUserId').value = userId;
            document.getElementById('reportedUserName').innerText = userName;
            openModal('reportMemberModal');
        }

        // إغلاق المودال عند الضغط على Escape
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape") {
                const modals = document.querySelectorAll('[id$="Modal"]');
                modals.forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        closeModal(modal.id);
                    }
                });
            }
        });
    </script>

@endsection