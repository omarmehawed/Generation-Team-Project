@extends('layouts.batu')
@section('content')
    <style>
        /* =========================================================
                                                                                               🔥 OMAR'S ULTIMATE MOBILE FIX V2 (Specific Patches) 🔥
                                                                                               ========================================================= */

        /* 1. إصلاح "Team Members" (قائمة الفريق) - المشكلة في الصورة الثانية */
        @media (max-width: 768px) {

            /* ترتيب رأس الجدول عمودياً بدل أفقياً */
            .bg-white.rounded-\[2rem\] .border-b.flex {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 1rem !important;
                padding: 1.5rem !important;
            }

            /* جعل زرار الإضافة والعداد ياخدوا عرض كامل ومسافة بينهم */
            .bg-white.rounded-\[2rem\] .border-b.flex>div:last-child {
                width: 100% !important;
                display: flex !important;
                justify-content: space-between !important;
                flex-wrap: wrap !important;
                gap: 10px !important;
            }

            /* إصلاح الجدول المقطوع - تفعيل السكرول */
            .overflow-x-auto {
                display: block !important;
                width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                padding-bottom: 15px !important;
            }

            /* تقليل الحواشي داخل خلايا الجدول */
            td,
            th {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
                white-space: nowrap !important;
                /* يمنع تكسر الكلام */
            }
        }

        /* 2. إصلاح "Team Card" (كارت الفيزا الأسود) - المشكلة في الصورة الثالثة */
        @media (max-width: 768px) {

            /* إلغاء الطول الثابت للكارت عشان ميبقاش ممطوط */
            .tilt-effect.h-72 {
                height: auto !important;
                min-height: auto !important;
                aspect-ratio: unset !important;
                display: flex !important;
                flex-direction: column !important;
            }

            /* تظبيط المحتوى الداخلي للكارت */
            .tilt-content {
                padding: 1.5rem !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 1.5rem !important;
                /* مسافات متساوية بين العناصر */
            }

            /* إصلاح الجزء السفلي (الكود) عشان ميكونش مقطوع */
            .tilt-content>div:last-child {
                margin-top: 0 !important;
                padding-bottom: 10px !important;
            }

            /* تصغير حجم الدرع (اللوجو) شوية */
            .fa-shield-alt {
                font-size: 1.5rem !important;
            }
        }

        /* 3. إصلاح الهيدر الرئيسي (Graduation Project) - المشكلة في الصورة الأولى */
        @media (max-width: 768px) {

            /* منع تداخل دائرة الديدلاين مع العنوان */
            .tilt-content .flex.items-center.gap-4.mb-5 {
                flex-wrap: wrap !important;
                margin-bottom: 1rem !important;
            }

            /* تصغير العنوان العملاق */
            h1.text-5xl {
                font-size: 2rem !important;
                line-height: 1.2 !important;
                margin-bottom: 1rem !important;
            }

            /* تظبيط النصوص الوصفية */
            p.text-gray-400.max-w-2xl {
                font-size: 0.9rem !important;
                line-height: 1.5 !important;
            }
        }

        /* 4. تحسينات عامة لكل الصفحة */
        @media (max-width: 768px) {

            /* ضبط الهوامش الخارجية للصفحة */
            .max-w-7xl {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
                padding-top: 5rem !important;
                /* مسافة من فوق عشان الموبايل */
            }

            /* تصغير المسافات الداخلية للكروت الكبيرة */
            .p-10,
            .p-8 {
                padding: 1.25rem !important;
            }

            /* التأكد من أن المودال (النوافذ المنبثقة) واخدة حجم الشاشة */
            .modal-content-styled {
                width: 90% !important;
                max-height: 80vh !important;
                overflow-y: scroll !important;
            }
        }
    </style>

    {{-- ========================================================= --}}
    {{-- 👑 المكتبات الخارجية (لإضافة الجرافيك والأنيميشن) --}}
    {{-- ========================================================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />

    {{-- ========================================================= --}}
    {{-- 👑 ستايلات "النسخة الملكية المحسنة" (Ultimate Royal Gold V2) --}}
    {{-- ========================================================= --}}
    <style>
        /* 🛠️ إصلاح مشكلة المودال (Modal Fix Core) */
        /* هذا الكلاس سيتم إضافته أوتوماتيكياً لأي مودال يفتح */
        .royal-modal-active {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 999999 !important;
            /* فوق أي شيء في الوجود */
            background-color: rgba(0, 0, 0, 0.75) !important;
            /* تعتيم الخلفية */
            backdrop-filter: blur(8px) !important;
            /* تأثير ضبابي قوي */
            overflow-y: auto !important;
            padding: 1rem !important;
        }

        /* منع السكرول في الصفحة الخلفية لما المودال يفتح */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 15px;
            /* لمنع قفزة السكرول بار */
        }

        /* شاشة التحميل (Preloader) */
        #royal-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0f0f0f;
            z-index: 99999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            transition: opacity 0.5s ease-out;
        }

        .loader-spinner {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(212, 175, 55, 0.3);
            border-radius: 50%;
            border-top-color: #D4AF37;
            animation: spin 1s ease-in-out infinite;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.5);
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* أنيميشن الظهور الانسيابي */
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

        /* أنيميشن اللمعة (Shimmer Effect) */
        @keyframes shimmer {
            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        /* أنيميشن الطفو (Floating) */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }

        /* التدرج الذهبي للنصوص */
        .text-gold-gradient {
            background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: shimmer 5s linear infinite;
            font-weight: 900;
            text-shadow: 0 0 10px rgba(191, 149, 63, 0.3);
        }

        /* خلفية الكروت الزجاجية الذهبية */
        .glass-gold {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.4);
            box-shadow: 0 10px 40px -10px rgba(212, 175, 55, 0.2);
        }

        /* الزرار الذهبي الأسطوري */
        .btn-royal-gold {
            background-image: linear-gradient(45deg, #b88746 0%, #fdf5a6 50%, #b88746 100%);
            background-size: 200% auto;
            color: #1a1a1a;
            border: none;
            transition: 0.5s;
            position: relative;
            overflow: hidden;
            z-index: 1;
            font-weight: bold;
            box-shadow: 0 0 20px rgba(184, 135, 70, 0.4);
        }

        .btn-royal-gold:hover {
            background-position: right center;
            transform: scale(1.02);
            box-shadow: 0 0 30px rgba(184, 135, 70, 0.7);
        }

        /* تأثير الهوفر للكروت */
        .hover-card-vip {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-card-vip:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 20px rgba(212, 175, 55, 0.3);
            border-color: #D4AF37;
            z-index: 10;
        }

        /* تحسينات الموبايل الشاملة */
        @media (max-width: 768px) {
            .mobile-stack {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .mobile-full {
                width: 100%;
            }

            .overflow-x-auto {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .overflow-x-auto::-webkit-scrollbar {
                display: none;
            }

            h1 {
                font-size: 1.875rem !important;
                line-height: 2.25rem !important;
            }

            h2 {
                font-size: 1.5rem !important;
                line-height: 2rem !important;
            }

            h3 {
                font-size: 1.125rem !important;
                line-height: 1.75rem !important;
            }
        }

        .rounded-super {
            border-radius: 1rem;
        }

        @media (min-width: 768px) {
            .rounded-super {
                border-radius: 2.5rem;
            }
        }

        /* 🔥🔥 إضافات اللمسة السحرية (The Magic Touch CSS) 🔥🔥 */
        .gold-ripple {
            position: absolute;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.7) 0%, rgba(255, 215, 0, 0) 70%);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
            z-index: 9999;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .mouse-glow {
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.06) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%);
            z-index: 9990;
            transition: background 0.3s;
            mix-blend-mode: screen;
        }

        /* 3D Tilt Class */
        .tilt-effect {
            transform-style: preserve-3d;
            transform: perspective(1000px);
        }

        .tilt-content {
            transform: translateZ(30px);
        }

        /* Custom Scrollbar */
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #D4AF37, #AA771C);
            border-radius: 10px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #b38728;
        }

        /* Animation Delays */
        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }

        /* تأثيرات إضافية للنصوص والحدود */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        /* تجميل المودال الداخلي (Container) */
        .modal-content-styled {
            background: var(--bg-panel); /* Use global variable */
            color: var(--text-main);
            border-radius: 1.5rem;
            border: 2px solid #D4AF37;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalPopIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* 🌙 Dark Mode / Light Mode Adaptations */
        .bg-white { background-color: var(--bg-panel); color: var(--text-main); }
        .text-gray-800, .text-gray-900 { color: var(--text-main); }
        .text-gray-500, .text-gray-400 { color: var(--text-muted); }
        .border-gray-100, .border-gray-200 { border-color: var(--border); }
        
        /* Table Headers */
        .bg-gray-50, .bg-gray-50\/50 { background-color: var(--bg-main); color: var(--text-muted); }

        /* Fix specific 'Join Team' and 'Team Card' issues */
        .tilt-effect.bg-gray-900 {
            background-color: var(--bg-panel); 
            border-color: var(--border);
        }
        [data-theme="light"] .tilt-effect.bg-gray-900 h3 { color: var(--text-main); }
        [data-theme="light"] .tilt-effect.bg-gray-900 i { color: var(--text-muted); }
        
        @keyframes modalPopIn {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>



    {{-- 🔥 شاشة التحميل (Preloader) 🔥 --}}
    <div id="royal-preloader">
        <div class="loader-spinner mb-4"></div>
        <h2 class="text-gold-gradient text-xl tracking-[0.3em] font-bold uppercase animate-pulse">Loading Workspace <br>
            Devloped by Omar Mehawed
        </h2>
    </div>

    {{-- لمعة الماوس --}}
    <div id="mouse-glow" class="mouse-glow"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10 fade-in-up">

        {{-- خلفية جمالية خفيفة في الصفحة --}}
        <div class="absolute top-0 right-0 -z-10 w-96 h-96 bg-yellow-400/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 -z-10 w-80 h-80 bg-orange-400/10 rounded-full blur-[100px]"></div>

        {{-- 🧭 1. شريط التنقل (زجاجي) --}}
        <nav class="flex mb-6 text-gray-500 text-xs md:text-sm font-medium overflow-x-auto pb-2" aria-label="Breadcrumb">
            <ol
                class="inline-flex items-center space-x-2 bg-white/70 backdrop-blur-md px-6 py-3 rounded-full shadow-lg border border-yellow-100/50 hover:shadow-xl transition-all duration-300">
                <li class="inline-flex items-center">
                    <a href="{{ route('projects.index') }}"
                        class="hover:text-yellow-600 transition-colors flex items-center gap-2 group">
                        <i class="fas fa-home group-hover:scale-125 transition-transform text-yellow-500"></i>
                        <span class="group-hover:font-bold transition-all">Projects</span>
                    </a>
                </li>
                <li><i class="fas fa-chevron-right text-yellow-300 text-xs mx-2"></i></li>
                <li>
                    <div class="flex items-center">
                        <span
                            class="text-yellow-900 font-bold bg-gradient-to-r from-yellow-200 to-yellow-100 px-4 py-1 rounded-full border border-yellow-300 shadow-sm animate-pulse">
                            <i class="fas fa-gem mr-1 text-yellow-700"></i> Generation Team Project
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- 📋 2. الهيدر الفخم (Black & Gold Theme) --}}
        <div
            class="tilt-effect relative rounded-[2rem] p-10 mb-16 overflow-hidden group hover:shadow-[0_20px_50px_rgba(212,175,55,0.3)] transition-all duration-500 bg-[#0f0f0f] text-white">
            {{-- تأثيرات الخلفية المتحركة --}}
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10">
            </div>
            <div
                class="absolute -right-20 -top-20 w-80 h-80 bg-gradient-to-br from-yellow-600 to-transparent rounded-full blur-3xl opacity-20 animate-pulse">
            </div>
            <div class="absolute left-10 bottom-10 w-40 h-40 bg-yellow-500 rounded-full blur-[80px] opacity-10"></div>

            {{-- بوردر ذهبي متدرج --}}
            <div
                class="absolute inset-0 rounded-[2rem] border-2 border-transparent bg-gradient-to-br from-yellow-500/30 to-transparent [mask:linear-gradient(#fff_0_0)_padding-box,linear-gradient(#fff_0_0)]">
            </div>

            <div
                class="tilt-content relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                <div>
                    <div class="flex items-center gap-4 mb-5">
                        <span
                            class="bg-gradient-to-r from-yellow-600 to-yellow-800 text-white text-xs font-black px-5 py-2 rounded-lg shadow-lg border border-yellow-500/50 tracking-[0.2em] uppercase transform hover:scale-105 transition-transform">
                            Premium Access
                        </span>
                        <span
                            class="text-gray-400 text-sm font-medium flex items-center gap-2 bg-white/5 px-4 py-1.5 rounded-full border border-white/10 backdrop-blur-sm hover:bg-white/10 transition-colors">
                            <i
                                class="far fa-clock {{ $project->deadline && \Carbon\Carbon::now()->gt($project->deadline) ? 'text-red-400' : 'text-yellow-400 animate-spin-slow' }}"></i>
                            <span class="text-gray-300">Deadline:</span>
                            <span
                                class="{{ $project->deadline && \Carbon\Carbon::now()->gt($project->deadline) ? 'text-red-400 line-through decoration-red-500' : 'text-yellow-100' }} font-bold tracking-wider">
                                @if ($project->deadline)
                                    {{ \Carbon\Carbon::parse($project->deadline)->format('d M, h:i A') }}
                                @else
                                    Coming Soon
                                @endif
                            </span>
                        </span>
                    </div>

                    <h1 class="text-5xl md:text-7xl font-black tracking-tighter mb-4 text-white drop-shadow-2xl">
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Generation</span>
                        <span class="text-gold-gradient block md:inline">Team Project</span>
                    </h1>
                    <p class="text-gray-400 mt-2 max-w-2xl text-lg font-light leading-relaxed">
                        Build the future with precision. Manage your team, track tasks, and deliver excellence.
                    </p>
                </div>

                @if ($team)
                    <div class="relative group/status animate-float">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-yellow-600 to-pink-600 rounded-2xl blur opacity-25 group-hover/status:opacity-75 transition duration-1000 group-hover/status:duration-200">
                        </div>
                        <div
                            class="relative px-8 py-6 bg-black rounded-2xl leading-none flex flex-col items-center border border-gray-800">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.3em] mb-2">System Status</p>
                            <div class="flex items-center gap-3">
                                <span class="flex h-4 w-4 relative">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-4 w-4 bg-gradient-to-r from-green-400 to-green-600 border-2 border-black"></span>
                                </span>
                                <span class="text-3xl font-bold text-white tracking-wide">Active</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- مكان الكود: جوه الهيدر الملكي، تحت الجزء بتاع حالة الطالب --}}
                <div class="mt-6 flex flex-wrap gap-4">
                    @php
                        $myRole =
                            $myRole ?? ($team?->members->where('user_id', auth()->id())->first()?->role ?? 'member');
                    @endphp

                    @if ($team && $myRole == 'leader' && in_array($team->proposal_status, ['pending', 'rejected']) && !$team->proposal_title)
                        <button onclick="openModal('proposalModal')"
                            class="relative px-6 py-3 bg-black rounded-xl font-bold text-white shadow-lg flex items-center gap-2 border border-gray-800 hover:bg-gray-800 transition transform hover:-translate-y-1">
                            <i class="fas fa-scroll"></i> Submit Proposal
                        </button>
                    @elseif($team && $team->proposal_title)
                        <div
                            class="bg-white/10 px-4 py-2 rounded-xl border border-white/20 text-gray-300 text-sm flex items-center gap-2 backdrop-blur-sm">
                            <i class="fas fa-file-alt text-[#FFD700]"></i>
                            Proposal:

                            @if ($team->proposal_file)
                                {{-- لاحظ هنا استخدمنا route بدلاً من asset --}}
                                <a href="{{ route('proposal.view_file', $team->id) }}" target="_blank"
                                    class="text-white font-bold hover:text-[#FFD700] hover:underline decoration-dashed underline-offset-4 transition-colors cursor-pointer"
                                    title="View Proposal File 📂">
                                    {{ \Illuminate\Support\Str::limit($team->proposal_title, 20) }}
                                </a>
                            @else
                                <span class="text-white font-bold">
                                    {{ \Illuminate\Support\Str::limit($team->proposal_title, 20) }}
                                </span>
                            @endif

                            @if ($team->proposal_status == 'pending')
                                <span class="bg-yellow-500/20 text-yellow-300 text-[10px] px-2 py-0.5 rounded ml-2">Under
                                    Review</span>
                            @elseif($team->proposal_status == 'approved')
                                <span
                                    class="bg-green-500/20 text-green-300 text-[10px] px-2 py-0.5 rounded ml-2">Approved</span>
                            @endif
                        </div>
                        @if ($team->proposal_status == 'approved' && $team->ta_id)
                            <div class="bg-blue-900/40 backdrop-blur-sm border-l-4 border-blue-500 p-4 rounded-r-xl">
                                <p class="text-[10px] font-bold text-blue-300 uppercase">Assigned Supervisor (TA)</p>
                                <p class="text-lg font-black text-white">{{ \App\Models\User::find($team->ta_id)->name }}
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- 🔄 المنطق الرئيسي (Logic) --}}
        {{-- ========================================== --}}
        @if (!$team)
            {{-- 🅰️ الحالة الأولى: ليس في فريق --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 fade-in-up delay-100">

                {{-- كارت إنشاء الفريق --}}
                <div
                    class="tilt-effect glass-gold p-10 rounded-[2.5rem] hover-card-vip group cursor-pointer relative overflow-hidden bg-white dark:bg-gray-800/50">
                    <div
                        class="absolute top-0 right-0 w-64 h-64 bg-yellow-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob">
                    </div>
                    <div
                        class="absolute -bottom-8 -left-8 w-64 h-64 bg-pink-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000">
                    </div>
                    <div class="tilt-content relative z-10 flex flex-col items-center text-center">
                        <div
                            class="w-28 h-28 bg-gradient-to-tr from-yellow-300 to-yellow-600 rounded-3xl rotate-6 flex items-center justify-center mb-8 shadow-[0_20px_40px_rgba(234,179,8,0.4)] group-hover:rotate-12 transition-all duration-500 border-4 border-white">
                            <i class="fas fa-crown text-6xl text-white drop-shadow-md"></i>
                        </div>
                        <h3 class="text-4xl font-black text-gray-800 mb-4 group-hover:text-yellow-700 transition-colors">
                            Create
                            Team</h3>
                        <p class="text-gray-500 mb-10 text-base leading-relaxed max-w-xs mx-auto">
                            Step up as a <span class="font-bold text-yellow-600">Leader</span>. Establish your squad and
                            dominate the project.
                        </p>
                        <button onclick="openModal('createTeamModal')"
                            class="w-full btn-royal-gold py-5 px-8 rounded-2xl shadow-xl flex items-center justify-center gap-4 text-lg transform active:scale-95">
                            <span>Initialize Team</span> <i
                                class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                        </button>
                    </div>
                </div>

                {{-- كارت الانضمام --}}
                <div
                    class="tilt-effect bg-gray-900 p-10 rounded-[2.5rem] hover-card-vip group cursor-pointer relative overflow-hidden border border-gray-800">
                    <div
                        class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>

                    <div class="tilt-content relative z-10 flex flex-col items-center text-center">
                        <div
                            class="w-28 h-28 bg-gradient-to-tr from-gray-700 to-gray-900 rounded-3xl -rotate-6 flex items-center justify-center mb-8 shadow-[0_20px_40px_rgba(0,0,0,0.6)] group-hover:-rotate-12 transition-all duration-500 border-4 border-gray-700">
                            <i class="fas fa-fingerprint text-6xl text-gray-300 drop-shadow-md"></i>
                        </div>
                        <h3 class="text-4xl font-black text-white mb-4 group-hover:text-gray-300 transition-colors">Join
                            Team
                        </h3>
                        <p class="text-gray-400 mb-10 text-base leading-relaxed max-w-xs mx-auto">
                            Got the <span class="font-bold text-yellow-500">Access Key</span>? Enter the secret code to
                            access
                            your workspace.
                        </p>
                        <button onclick="openModal('joinTeamModal')"
                            class="w-full bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-white text-black font-black py-5 px-8 rounded-2xl shadow-[0_0_30px_rgba(255,255,255,0.2)] transition-all flex items-center justify-center gap-4 text-lg transform active:scale-95">
                            Enter Access Code <i
                                class="fas fa-key text-yellow-600 group-hover:rotate-45 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($team) && $team)
            {{-- 🅱️ الحالة الثانية: داخل فريق (الداشبورد البريميوم) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 fade-in-up delay-100">
                {{-- العمود الجانبي (4 أعمدة) --}}
                <div class="lg:col-span-4 space-y-8">

                    {{-- كارت الفريق (شكل الفيزا كارد) --}}
                    {{-- كارت الفريق (شكل الفيزا كارد) --}}
                    {{-- 🔥 التعديل: شلنا min-h عشان الكارت ميبقاش طويل بزيادة، وخليناه h-auto --}}
                    <div
                        class="tilt-effect relative h-auto rounded-3xl overflow-hidden shadow-2xl group transition-transform hover:scale-[1.02] duration-300">

                        {{-- الخلفية السوداء والذهبية --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 to-black"></div>
                        <div
                            class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30">
                        </div>

                        {{-- تأثيرات الإضاءة --}}
                        <div
                            class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-yellow-400 to-yellow-700 rounded-full blur-2xl opacity-40">
                        </div>
                        <div class="absolute bottom-10 -left-10 w-20 h-20 bg-yellow-500 rounded-full blur-xl opacity-20">
                        </div>

                        {{-- المحتوى --}}
                        {{-- 🔥 التعديل: شلنا h-full و justify-between عشان المحتوى يبقى ورا بعضه بشكل طبيعي --}}
                        <div class="tilt-content relative z-10 p-6 border border-yellow-500/30 rounded-3xl">

                            {{-- الجزء العلوي --}}
                            <div class="flex justify-between items-start gap-4">
                                <div class="space-y-4 w-full">

                                    {{-- ⭐⭐⭐ اللوجو وزرار التعديل ⭐⭐⭐ --}}
                                    <div class="relative group/badge w-fit">
                                        <div
                                            class="w-24 h-24 rounded-full p-1 bg-gradient-to-tr from-[#D4AF37] to-transparent shadow-lg relative">

                                            {{-- الصورة --}}
                                            <img src="{{ $team->logo ? route('final_project.logo', $team->id) . '?v=' . time() : 'https://ui-avatars.com/api/?name=' . urlencode($team->name) . '&background=000&color=D4AF37&bold=true' }}"
                                                alt="Team Logo"
                                                class="w-full h-full rounded-full object-cover border-4 border-black bg-gray-900">

                                            {{-- زرار التعديل --}}
                                            @if (isset($myRole) && $myRole == 'leader')
                                                <button onclick="openModal('editLogoModal')"
                                                    class="absolute bottom-0 right-0 bg-white text-black w-8 h-8 flex items-center justify-center rounded-full shadow-lg border border-gray-200 hover:bg-[#D4AF37] hover:text-white transition-all transform hover:scale-110 cursor-pointer z-20">
                                                    <i class="fas fa-camera text-xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- بيانات التيم --}}
                                    <div class="space-y-3">
                                        <div>
                                            <p
                                                class="text-yellow-500 text-[10px] font-bold tracking-[0.2em] uppercase mb-1">
                                                Team Name</p>
                                            <h2
                                                class="text-white text-lg font-bold tracking-widest uppercase drop-shadow-md leading-tight break-words">
                                                {{ $team->name }}
                                            </h2>
                                        </div>

                                        <div>
                                            <p
                                                class="text-yellow-500 text-[10px] font-bold tracking-[0.2em] uppercase mb-1">
                                                Project Name</p>
                                            <p
                                                class="text-gray-300 text-xs font-bold tracking-widest uppercase break-words">
                                                {{ $team->proposal_title ?? 'Project Title Pending' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p
                                                class="text-yellow-500 text-[10px] font-bold tracking-[0.2em] uppercase mb-1">
                                                Monitored By</p>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-chalkboard-teacher text-gray-400 text-xs"></i>
                                                <p class="text-white text-xs font-bold tracking-wider uppercase">
                                                    @if ($team->ta_id)
                                                        {{ \App\Models\User::find($team->ta_id)->name ?? 'Unknown' }}
                                                    @else
                                                        <span class="text-gray-500 italic">Not Assigned Yet</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- أيقونة الدرع --}}
                                <i class="fas fa-shield-alt text-4xl text-yellow-600 opacity-80 flex-shrink-0"></i>
                            </div>

                            {{-- الجزء السفلي: كود التيم --}}
                            {{-- المسافة mt-6 بتفصل الكود عن البيانات اللي فوقه بشكل شيك --}}
                            <div class="mt-8">
                                <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10 group/code cursor-pointer relative overflow-hidden transition hover:bg-white/20"
                                    onclick="copyCode('{{ $team->code }}')">
                                    <div class="absolute top-0 left-0 h-full w-1 bg-yellow-500"></div>
                                    <p class="text-[9px] text-gray-400 uppercase tracking-widest mb-1 ml-2">Secure Access
                                        Code</p>
                                    <div class="flex items-center justify-between ml-2">
                                        <span
                                            class="text-xl font-mono font-bold text-white tracking-[0.2em] group-hover/code:text-yellow-400 transition-colors">
                                            {{ $team->code }}
                                        </span>
                                        <button class="text-gray-400 hover:text-white">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- كارت حالتي (My Role) --}}
                    <div
                        class="bg-white rounded-3xl border border-gray-100 shadow-xl p-8 relative overflow-hidden group hover:border-yellow-300 transition-colors">
                        <div
                            class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-gray-200 to-gray-400 group-hover:from-yellow-400 group-hover:to-yellow-600 transition-all duration-500">
                        </div>

                        <h3 class="text-xs font-bold text-gray-400 uppercase mb-6 tracking-widest flex items-center gap-2">
                            <i class="fas fa-id-badge text-yellow-500"></i> My Identity
                        </h3>

                        @php
                            $myMember = $team->members->where('user_id', auth()->id())->first();
                            $myRole = $myMember ? $myMember->role : 'member';
                            $isLocked = false;
                            if ($project->deadline) {
                                $isLocked = \Carbon\Carbon::now()->gt($project->deadline);
                            }
                        @endphp

                        <div class="flex flex-col items-center mb-8">
                            <div
                                class="w-20 h-20 rounded-full flex items-center justify-center mb-4 shadow-lg relative {{ $myRole == 'leader' ? 'bg-gradient-to-br from-yellow-300 to-yellow-600 text-white border-4 border-yellow-100' : 'bg-gray-100 text-gray-500 border-4 border-white' }}">
                                <i
                                    class="fas {{ $myRole == 'leader' ? 'fa-crown text-3xl animate-pulse' : 'fa-user text-3xl' }}"></i>
                            </div>
                            <span class="text-2xl font-black text-gray-800 capitalize">{{ ucfirst($myRole) }}</span>
                            <span class="text-xs text-gray-400 mt-1">Authorized Personnel</span>
                        </div>

                        {{-- 🔥🔥 الزرار الذكي (بيقفل ويفتح حسب الديدلاين) 🔥🔥 --}}
                        @if ($isLocked)
                            <div class="space-y-2">
                                <button disabled
                                    class="w-full bg-gray-100 text-gray-400 font-bold rounded-xl py-3.5 cursor-not-allowed flex items-center justify-center gap-2 border border-gray-200 shadow-inner">
                                    <i class="fas fa-lock"></i>
                                    <span>Team Locked</span>
                                </button>
                                <p class="text-[10px] text-center text-red-400 font-bold uppercase tracking-wide">Deadline
                                    Passed
                                </p>
                            </div>
                        @else
                            <button onclick="openModal('leaveTeamModal')"
                                class="w-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white font-bold rounded-xl py-3.5 transition flex items-center justify-center gap-2 group/btn shadow-sm hover:shadow-red-200">
                                <i class="fas fa-sign-out-alt group-hover/btn:-translate-x-1 transition-transform"></i>
                                <span>Disengage</span>
                            </button>
                        @endif
                    </div>
                    {{-- 🏟️ Lobby Card (Always Visible for Leader) --}}
                    @if(isset($myRole) && $myRole == 'leader')
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl p-6 relative overflow-hidden group hover:border-blue-300 transition-colors mb-8">
                        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-400 to-cyan-500"></div>

                        <h3 class="text-xs font-bold text-gray-400 uppercase mb-4 tracking-widest flex items-center gap-2">
                            <i class="fas fa-door-open text-blue-500"></i> Lobby <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full text-[10px]">{{ isset($pendingMembers) ? count($pendingMembers) : 0 }}</span>
                        </h3>

                        <div class="space-y-4 max-h-[300px] overflow-y-auto custom-scroll pr-2">
                            @if(isset($pendingMembers) && count($pendingMembers) > 0)
                                @foreach($pendingMembers as $req)
                                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($req->user->name) }}&background=EBF8FF&color=3182CE&bold=true" class="w-10 h-10 rounded-full border border-blue-200">
                                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-yellow-400 border-2 border-white rounded-full" title="Pending"></span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-800">{{ \Illuminate\Support\Str::limit($req->user->name, 15) }}</h4>
                                            <p class="text-[10px] text-gray-400">{{ $req->created_at->diffForHumans(null, true) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <form action="{{ route('final_project.approve_member', $req->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center hover:bg-green-500 hover:text-white transition-colors shadow-sm" title="Accept">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('final_project.reject_member', $req->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 rounded-full bg-red-100 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors shadow-sm" title="Reject">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-8 text-gray-400">
                                    <i class="fas fa-wind text-2xl mb-2 text-gray-300"></i>
                                    <p class="text-xs">Lobby is empty</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div> {{-- 🛑🛑 هنا قفلة الـ Sidebar --}}





                {{-- العمود الرئيسي (8 أعمدة) --}}
                <div class="lg:col-span-8 space-y-8">


                    {{-- 1. جدول الأعضاء (The Team Members) --}}
                    <div x-data="{ expanded: false }" class="bg-white rounded-[2rem] border border-gray-100 shadow-2xl overflow-hidden relative">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-50 rounded-bl-[100%] -mr-10 -mt-10 z-0">
                        </div>
                        <div @click="expanded = !expanded" class="px-8 py-8 border-b border-gray-100 flex justify-between items-center relative z-10 cursor-pointer hover:bg-gray-50 transition-colors">
                            <div>
                                <h3 class="text-2xl font-black text-gray-800 flex items-center gap-3">
                                    The Team Members
                                    <i class="fas fa-chevron-down text-lg text-gray-300 transition-transform duration-300 ml-2" :class="expanded ? 'rotate-180' : ''"></i>
                                </h3>
                                <p class="text-sm text-gray-400 mt-1">Manage your elite team members</p>
                            </div>

                            <div class="flex items-center gap-4">
                                @if(auth()->user()->email === '2420823@batechu.com')
                                    <button @click.stop onclick="openExportModal('all')" class="bg-green-50 text-green-600 border border-green-200 px-4 py-2.5 rounded-xl text-xs font-bold shadow-sm hover:bg-green-500 hover:text-white transition flex items-center gap-2">
                                        <i class="fas fa-file-excel text-base"></i> Export Excel
                                    </button>
                                @endif
                                <span
                                    class="bg-black text-yellow-400 text-xs font-bold py-2 px-4 rounded-full shadow-lg border border-gray-700">
                                    {{ $team->members->count() }} / 60 Total
                                </span>
                                @if ($myRole == 'leader')
                                    <button @click.stop onclick="openModal('inviteMemberModal')"
                                        class="btn-royal-gold px-6 py-2.5 rounded-xl shadow-lg flex items-center gap-2 hover:-translate-y-1 transform font-bold text-sm">
                                        <i class="fas fa-plus"></i> Add Member
                                    </button>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Collapsible Body --}}
                        <div x-show="expanded" x-transition.opacity.duration.300ms style="display: none;">
                            <div class="overflow-x-auto px-8 pt-4 pb-6 custom-scroll max-h-[500px] overflow-y-auto">
                            <table class="w-full text-left border-separate border-spacing-y-3">
                                <thead>
                                    <tr class="text-xs text-gray-400 uppercase tracking-wider">
                                        <th class="px-6 py-2 font-light">Member Profile</th>
                                        <th class="px-6 py-2 font-light">Academic Number</th>
                                        <th class="px-6 py-2 font-light">Rank</th>
                                        <th class="px-6 py-2 text-right font-light">Controls</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($team->members as $member)
                                        <tr class="group transition-all duration-300 hover:-translate-y-1">
                                            <td class="bg-white group-hover:bg-yellow-50/30 rounded-l-2xl border-y border-l border-gray-100 group-hover:border-yellow-200 shadow-sm group-hover:shadow-md px-6 py-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="relative">
                                                        <x-user-avatar :user="$member->user" size="w-12 h-12" classes="border-2 border-white shadow-md" />
                                                        @if ($member->role == 'leader')
                                                            <div class="absolute -top-2 -right-2 bg-yellow-500 text-white w-5 h-5 rounded-full flex items-center justify-center shadow-sm text-[10px]">
                                                                <i class="fas fa-crown"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="text-base font-bold text-gray-800 group-hover:text-yellow-800 transition-colors">
                                                            {{ $member->user->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-400">
                                                            {{ $member->user->email ?? 'Dev Team' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bg-white group-hover:bg-yellow-50/30 border-y border-gray-100 group-hover:border-yellow-200 shadow-sm group-hover:shadow-md px-6 py-4">
                                                <div class="text-sm font-bold text-gray-700 font-mono bg-gray-50 px-3 py-1.5 rounded-lg inline-block border border-gray-200 shadow-sm">
                                                    {{ $member->user->email ? explode('@', $member->user->email)[0] : 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="bg-white group-hover:bg-yellow-50/30 border-y border-gray-100 group-hover:border-yellow-200 shadow-sm group-hover:shadow-md px-6 py-4">
                                                @if ($member->role == 'leader')
                                                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 text-xs font-black shadow-sm border border-yellow-300">
                                                        <i class="fas fa-star text-[10px] animate-spin-slow"></i> TEAM LEADER
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-gray-50 text-gray-500 text-xs font-bold border border-gray-200">
                                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span> Member
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-8 py-4 text-right bg-white group-hover:bg-yellow-50/30 rounded-r-2xl border-y border-r border-gray-100 group-hover:border-yellow-200 shadow-sm group-hover:shadow-md">
                                                <div class="flex items-center justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                                    @php
                                                        $isLeaderManaging = $myRole == 'leader' && $member->role != 'leader';
                                                        $isViceManaging = $myRole == 'vice_leader' && $member->role == 'member';
                                                    @endphp

                                                    @if ($isLeaderManaging || $isViceManaging)
                                                        <a href="{{ route('profile.show', $member->user->id) }}"
                                                            class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white transition flex items-center justify-center shadow-sm transform hover:scale-110"
                                                            title="View Profile">
                                                            <i class="fas fa-user-circle text-xs"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    @if ($myRole == 'leader' && $member->role != 'leader')
                                                        <form action="{{ route('final_project.remove_member') }}"
                                                            method="POST" class="inline-block"
                                                            onsubmit="return confirmAction(event, 'Remove this member?')">
                                                            @csrf
                                                            <input type="hidden" name="team_id" value="{{ $team->id }}">
                                                            <input type="hidden" name="user_id" value="{{ $member->user_id }}">
                                                            <button class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center justify-center shadow-sm transform hover:scale-110"
                                                                title="Remove">
                                                                <i class="fas fa-trash-alt text-xs"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <button onclick="openReportModal('{{ $member->user_id }}', '{{ $member->user->name }}')"
                                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-800 hover:text-white transition flex items-center justify-center shadow-sm transform hover:scale-110"
                                                        title="Report">
                                                        <i class="fas fa-flag text-xs"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </div> {{-- End Collapsible Body --}}
                    </div>

                    {{-- Group Cards Removed --}}

                    {{-- 🔥 2. اجتماعات التيم الداخلية (Internal Meetings) 🔥 --}}
                    <div
                        class="bg-white rounded-[2.5rem] border border-gray-200 shadow-xl overflow-hidden hover-lift mt-10 transition-all hover:shadow-2xl">
                        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 flex items-center gap-3 text-lg">
                                <div class="p-2 bg-blue-100 rounded-lg text-blue-600 shadow-sm"><i
                                        class="fas fa-users"></i>
                                </div>
                                Internal Meetings
                            </h3>

                            <div class="flex gap-2">
                                <button onclick="openModal('internalHistoryModal')"
                                    class="bg-gray-100 text-gray-500 hover:text-gray-700 px-3 py-2 rounded-xl text-xs font-bold transition border border-gray-200 hover:bg-gray-200">
                                    <i class="fas fa-history"></i> Log
                                </button>

                                @if ($myRole == 'leader')
                                    <button onclick="openModal('internalMeetingModal')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl shadow-lg transition transform hover:scale-105 font-bold text-xs flex items-center gap-2">
                                        <i class="fas fa-plus"></i> Schedule
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="p-8">
                            @php
                                $nextInternal = \App\Models\Meeting::where('team_id', $team->id)
                                    ->where('type', 'internal')
                                    ->where('status', 'confirmed')
                                    ->orderBy('meeting_date', 'asc')
                                    ->first();
                            @endphp
                            @if ($nextInternal)
                                <div
                                    class="bg-blue-50 rounded-2xl p-6 border border-blue-100 flex flex-col md:flex-row justify-between items-center gap-4 transition-transform hover:scale-[1.01]">
                                    <div>
                                        <p class="font-black text-gray-800 text-lg mb-1">{{ $nextInternal->topic }}</p>
                                        <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                                            <span><i class="far fa-clock text-blue-500"></i>
                                                {{ \Carbon\Carbon::parse($nextInternal->meeting_date)->format('D, d M - h:i A') }}</span>
                                            <span>
                                                @if ($nextInternal->mode == 'online')
                                                    🌐 Online
                                                @else
                                                    📍
                                                    {{ $nextInternal->location ?? 'Offline' }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 w-full md:w-auto">
                                        @if ($nextInternal->meeting_link)
                                            <a href="{{ $nextInternal->meeting_link }}" target="_blank"
                                                class="flex-1 md:flex-none bg-blue-600 text-white py-2 px-6 rounded-xl text-xs font-bold text-center hover:bg-blue-700 transition shadow-md hover:shadow-blue-300">Join</a>
                                        @endif

                                        @if ($myRole == 'leader')
                                            <button
                                                onclick="openAttendanceModal('{{ $nextInternal->id }}', '{{ $nextInternal->topic }}')"
                                                class="flex-1 md:flex-none bg-white border border-blue-200 text-blue-600 py-2 px-6 rounded-xl text-xs font-bold hover:bg-blue-50 transition">Attendance
                                                📝</button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8 border-2 border-dashed border-gray-100 rounded-2xl">
                                    <p class="text-gray-400 text-sm">No internal meetings scheduled.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- استدعاء المودالات --}}
        @include('final_project.partials.modals')

        {{-- ========================================== --}}
        {{-- 🔔 منطقة التنبيهات (Status Banners) --}}
        {{-- ========================================== --}}
        @if ($team && $team->proposal_status)
            <div class="mb-10 fade-in-up delay-200 mt-8">
                {{-- 🟡 حالة الانتظار --}}
                @if ($team->proposal_status == 'pending')
                    <div
                        class="bg-gradient-to-r from-yellow-50 to-white border-l-4 border-[#D4AF37] p-6 rounded-2xl shadow-lg flex items-center gap-4 relative overflow-hidden transform hover:-translate-y-1 transition">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-[#D4AF37]/10 rounded-full blur-2xl -mr-10 -mt-10">
                        </div>
                        <div class="p-3 bg-[#FFF8E1] rounded-full text-[#AA8A26] animate-pulse">
                            <i class="fas fa-hourglass-half text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">Proposal Under Review</h4>
                            <p class="text-sm text-gray-500">Your proposal "<span
                                    class="font-bold text-[#AA8A26]">{{ $team->proposal_title }}</span>" is currently
                                being reviewed
                                by the professor. Tasks are locked until approval.</p>
                        </div>
                    </div>
                    {{-- 🔴 حالة الرفض --}}
                @elseif($team->proposal_status == 'rejected')
                    <div
                        class="bg-gradient-to-r from-red-50 to-white border-l-4 border-red-500 p-6 rounded-2xl shadow-lg flex items-start gap-4 transform hover:-translate-y-1 transition">
                        <div class="p-3 bg-red-100 rounded-full text-red-600">
                            <i class="fas fa-times-circle text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-red-800">Proposal Rejected</h4>
                            <p class="text-sm text-red-600 mt-1"><strong>Reason:</strong>
                                {{ $team->rejection_reason ?? 'Does not meet requirements.' }}</p>
                            @if ($myRole == 'leader')
                                <button onclick="openModal('proposalModal')"
                                    class="mt-4 text-xs bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition shadow-md">
                                    <i class="fas fa-redo mr-1"></i> Resubmit Proposal
                                </button>
                            @endif
                        </div>
                    </div>
                    {{-- 🟢 حالة القبول --}}
                @elseif($team->proposal_status == 'approved')
                    <div
                        class="bg-gradient-to-r from-green-50 to-white border-l-4 border-green-500 p-6 rounded-2xl shadow-lg flex items-center gap-4 relative overflow-hidden transform hover:-translate-y-1 transition">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-green-500/10 rounded-full blur-2xl -mr-10 -mt-10">
                        </div>
                        <div class="p-3 bg-green-100 rounded-full text-green-600">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-green-800">Proposal Approved! 🚀</h4>
                            <p class="text-sm text-green-600">Congratulations! Your topic has been approved. You can now
                                access the
                                Task Board.</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- 🔥🔥 (1) بداية القفل: الكود ده هيخفي كل شغلك اللي تحت لو مش مقبول 🔥🔥 --}}
        @if ($team && $team->proposal_status == 'approved')

            {{-- 5. مركز الاجتماعات (Meetings Control Center) --}}
            <div
                class="bg-gradient-to-br from-gray-900 to-black rounded-[2.5rem] border border-[#D4AF37]/30 shadow-2xl p-8 relative overflow-hidden group hover-lift w-full mb-10">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#D4AF37] to-transparent"></div>
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h4 class="text-[#FFD700] font-bold text-lg flex items-center gap-2">
                            <i class="fas fa-chalkboard-teacher"></i> Supervision
                        </h4>
                        <p class="text-gray-400 text-xs mt-1">Formal meetings with Doctor/Assistant.</p>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="openModal('supervisionHistoryModal')"
                            class="bg-white/10 text-gray-300 hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition border border-white/10 hover:bg-white/20">
                            <i class="fas fa-history"></i> Log
                        </button>
                        @if ($myRole == 'leader')
                            <button onclick="openModal('bookSupervisionModal')"
                                class="bg-[#D4AF37] hover:bg-[#AA8A26] text-white px-4 py-2 rounded-xl shadow-lg transition transform hover:scale-105 font-bold text-xs flex items-center gap-2 box-shadow-gold">
                                <i class="fas fa-plus"></i> Request
                            </button>
                        @endif
                    </div>
                </div>

                @php
                    $nextSupervision = null;
                    if (isset($team) && $team) {
                        $nextSupervision = \App\Models\Meeting::where('team_id', $team->id)
                            ->where('type', 'supervision')
                            ->whereIn('status', ['pending', 'confirmed'])
                            ->orderBy('meeting_date', 'asc')
                            ->first();
                    }
                @endphp

                @if ($nextSupervision)
                    <div
                        class="bg-white/10 rounded-2xl p-6 border border-white/10 backdrop-blur-sm flex flex-col md:flex-row justify-between items-center gap-4 transition-transform hover:scale-[1.01]">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center text-[#FFD700] text-2xl relative">
                                @if ($nextSupervision->mode == 'online')
                                    <i class="fas fa-video"></i>
                                    <span
                                        class="absolute -bottom-1 -right-1 bg-blue-500 text-white text-[8px] px-1.5 py-0.5 rounded font-bold uppercase">Online</span>
                                @else
                                    <i class="fas fa-university"></i>
                                    <span
                                        class="absolute -bottom-1 -right-1 bg-purple-500 text-white text-[8px] px-1.5 py-0.5 rounded font-bold uppercase">Offline</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-white font-bold text-xl">
                                    {{ \Carbon\Carbon::parse($nextSupervision->meeting_date)->format('D, d M') }}
                                </p>
                                <p class="text-[#D4AF37] font-mono text-sm">
                                    {{ \Carbon\Carbon::parse($nextSupervision->meeting_date)->format('h:i A') }}
                                </p>
                                <p class="text-gray-400 text-xs mt-1 max-w-md truncate">{{ $nextSupervision->topic }}</p>
                                @if ($nextSupervision->description)
                                    <p class="text-gray-500 text-[10px] mt-1">
                                        {{ Str::limit($nextSupervision->description, 50) }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-3 w-full md:w-auto">
                            <span
                                class="px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider w-full md:w-auto text-center {{ $nextSupervision->status == 'confirmed' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' }}">
                                {{ $nextSupervision->status }}
                            </span>

                            @if ($nextSupervision->status == 'confirmed' && $nextSupervision->mode == 'online')
                                @if ($nextSupervision->meeting_link)
                                    <a href="{{ $nextSupervision->meeting_link }}" target="_blank"
                                        class="w-full md:w-auto text-center bg-blue-600 hover:bg-blue-500 text-white py-2 px-6 rounded-xl text-xs font-bold transition shadow-lg hover:shadow-blue-500/50 flex items-center justify-center gap-2 animate-pulse">
                                        <i class="fas fa-video"></i> Click to Join Meeting
                                    </a>
                                @else
                                    <span class="text-xs text-yellow-500 font-bold bg-yellow-50/10 px-2 py-1 rounded">Link
                                        pending...</span>
                                @endif
                            @elseif($nextSupervision->status == 'confirmed' && $nextSupervision->mode == 'offline')
                                <div
                                    class="flex items-center gap-2 bg-gray-800 text-white py-2 px-4 rounded-xl text-xs font-bold border border-gray-700 w-full md:w-auto justify-center">
                                    <i class="fas fa-map-marker-alt text-[#FFD700]"></i>
                                    <span>{{ $nextSupervision->location ?? 'Location TBA' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 border-2 border-dashed border-white/10 rounded-2xl">
                        <p class="text-gray-500 text-sm">No upcoming supervision sessions.</p>
                    </div>
                @endif
            </div>

            {{-- 2. لوحة المهام المقسمة (Split Tasks Board) --}}
            <div id="tasks-section" x-data="{ expanded: false }"
                class="bg-white rounded-[2.5rem] border border-gray-200 shadow-xl overflow-hidden hover-lift relative mt-10">
                <div @click="expanded = !expanded" class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors">
                    <h3 class="font-bold text-gray-800 flex items-center gap-3 text-lg">
                        <div class="p-2 bg-orange-50 rounded-xl text-orange-500 shadow-sm"><i
                                class="fas fa-layer-group"></i>
                        </div>
                        Tasks Distribution
                        <i class="fas fa-chevron-down text-sm text-gray-400 transition-transform duration-300 ml-1" :class="expanded ? 'rotate-180' : ''"></i>
                    </h3>
                </div>
                
                {{-- Collapsible Body --}}
                <div x-show="expanded" x-transition.opacity.duration.300ms style="display: none;">

                {{-- تجهيز المتغيرات عشان نتفادى مشاكل الحروف الكابيتال والسمول --}}
                @php
                    $myTechRole = strtolower($myMember->technical_role ?? ''); // بنحولها حروف صغيرة
                    $isLeader = $myRole === 'leader';
                    $isSoftVice = $myRole === 'vice_leader' && $myTechRole === 'software';
                    $isHardVice = $myRole === 'vice_leader' && $myTechRole === 'hardware';
                @endphp

                <div class="grid grid-cols-1 xl:grid-cols-2 divide-y xl:divide-y-0 xl:divide-x divide-gray-100">

                    {{-- 💻 الجزء الأول: Software Team --}}
                    <div class="p-6 bg-blue-50/30">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="font-black text-blue-800 text-lg flex items-center gap-2">
                                <i class="fas fa-laptop-code"></i> Software
                            </h4>

                            {{-- زرار السوفتوير: يظهر لليدر أو نائب السوفتوير --}}
                            @if ($isLeader || $isSoftVice)
                                <button onclick="openAddTaskModal('software')"
                                    class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg shadow hover:bg-blue-700 transition font-bold hover:scale-105 transform">
                                    <i class="fas fa-plus"></i> Assign
                                </button>
                            @endif
                        </div>

                        @php $softMembers = $team->members->where('technical_role', 'software'); @endphp
                        <div class="space-y-4">
                            @foreach ($softMembers as $member)
                                @php $memberTasks = $team->tasks->where('user_id', $member->user_id); @endphp
                                <div
                                    class="bg-white rounded-xl p-4 border border-blue-100 shadow-sm hover:shadow-md transition">
                                    <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-50">
                                        <img class="w-8 h-8 rounded-full"
                                            src="https://ui-avatars.com/api/?name={{ $member->user->name }}">
                                        <div>
                                            <p class="text-xs font-bold text-gray-800">{{ $member->user->name }}</p>
                                            <span
                                                class="text-[9px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded">{{ ucfirst($member->role) }}</span>
                                        </div>
                                    </div>

                                    @if ($memberTasks->count() > 0)
                                        <div class="space-y-2">
                                            @foreach ($memberTasks as $task)
                                                @include('final_project.partials.task_card', [
                                                    'task' => $task,
                                                    'color' => 'blue',
                                                ])
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[10px] text-gray-400 text-center italic">No tasks assigned.</p>
                                    @endif
                                </div>
                            @endforeach

                            @if ($softMembers->count() == 0)
                                <p class="text-center text-xs text-gray-400 py-4">No Software members assigned yet.</p>
                            @endif
                        </div>
                    </div>

                    {{-- 🔌 الجزء الثاني: Hardware Team --}}
                    <div class="p-6 bg-orange-50/30">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="font-black text-orange-800 text-lg flex items-center gap-2">
                                <i class="fas fa-microchip"></i> Hardware
                            </h4>

                            {{-- زرار الهاردوير: يظهر لليدر أو نائب الهاردوير --}}
                            @if ($isLeader || $isHardVice)
                                <button onclick="openAddTaskModal('hardware')"
                                    class="text-xs bg-orange-600 text-white px-3 py-1.5 rounded-lg shadow hover:bg-orange-700 transition font-bold hover:scale-105 transform">
                                    <i class="fas fa-plus"></i> Assign
                                </button>
                            @endif
                        </div>

                        @php $hardMembers = $team->members->where('technical_role', 'hardware'); @endphp
                        <div class="space-y-4">
                            @foreach ($hardMembers as $member)
                                @php $memberTasks = $team->tasks->where('user_id', $member->user_id); @endphp
                                <div
                                    class="bg-white rounded-xl p-4 border border-orange-100 shadow-sm hover:shadow-md transition">
                                    <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-50">
                                        <img class="w-8 h-8 rounded-full"
                                            src="https://ui-avatars.com/api/?name={{ $member->user->name }}">
                                        <div>
                                            <p class="text-xs font-bold text-gray-800">{{ $member->user->name }}</p>
                                            <span
                                                class="text-[9px] bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded">{{ ucfirst($member->role) }}</span>
                                        </div>
                                    </div>

                                    @if ($memberTasks->count() > 0)
                                        <div class="space-y-2">
                                            @foreach ($memberTasks as $task)
                                                @include('final_project.partials.task_card', [
                                                    'task' => $task,
                                                    'color' => 'orange',
                                                ])
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[10px] text-gray-400 text-center italic">No tasks assigned.</p>
                                    @endif
                                </div>
                            @endforeach

                            @if ($hardMembers->count() == 0)
                                <p class="text-center text-xs text-gray-400 py-4">No Hardware members assigned yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
                </div> {{-- End Collapsible Body --}}
            </div>
            {{-- 3. قسم الميزانية والتمويل (Split View) --}}
            <div id="budget-section" x-data="{ expandedParent: false }" class="mt-12">
                {{-- العنوان الرئيسي للقسم --}}
                <div @click="expandedParent = !expandedParent" class="flex items-center gap-3 mb-6 cursor-pointer group w-max">
                    <div class="p-3 bg-green-100 rounded-xl text-green-600 shadow-sm group-hover:scale-110 transition-transform"><i
                            class="fas fa-wallet text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 flex items-center gap-2">
                        Expenses &amp; Budget
                        <i class="fas fa-chevron-down text-lg text-gray-400 transition-transform duration-300 ml-2 group-hover:text-gray-600" :class="expandedParent ? 'rotate-180' : ''"></i>
                    </h3>
                </div>
                <div x-show="expandedParent" x-transition.opacity.duration.300ms style="display: none;" class="grid grid-cols-1 xl:grid-cols-3 gap-8 mt-10">

                    {{-- ⚙️ الجزء الأول: المكونات (Components) --}}
                    <div x-data="{ expanded: false }" class="bg-white rounded-[2.5rem] border border-blue-100 shadow-xl overflow-hidden hover-lift flex flex-col">
                        <div @click="expanded = !expanded" class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-blue-50/40 cursor-pointer hover:bg-blue-50 transition-colors">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <div class="p-2 bg-blue-100 rounded-lg text-blue-600 shadow-sm"><i class="fas fa-microchip"></i></div>
                                Components
                                <i class="fas fa-chevron-down text-sm text-gray-400 transition-transform duration-300 ml-1" :class="expanded ? 'rotate-180' : ''"></i>
                            </h3>
                            @if ($myRole == 'leader')
                                <button @click.stop onclick="openModal('addComponentModal')"
                                    class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-bold shadow-md transition flex items-center gap-1">
                                    <i class="fas fa-plus"></i> Add Component
                                </button>
                            @endif
                        </div>

                        <div x-show="expanded" x-transition.opacity.duration.300ms style="display: none;" class="flex flex-col flex-grow">
                        <div class="p-6">
                            @if ($components->count() > 0)
                                <div class="space-y-4 overflow-y-auto max-h-80 custom-scroll">
                                    @foreach ($components as $comp)
                                        <div class="flex items-start gap-3 p-3 rounded-2xl bg-gray-50 border border-gray-100 hover:border-blue-200 hover:bg-blue-50/30 transition">
                                            {{-- Component Image --}}
                                            @if ($comp->image_path)
                                                <img src="{{ $comp->image_path }}" alt="{{ $comp->name }}"
                                                    class="w-14 h-14 rounded-xl object-cover border border-gray-200 flex-shrink-0">
                                            @else
                                                <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center border border-blue-200 flex-shrink-0">
                                                    <i class="fas fa-microchip text-blue-400 text-xl"></i>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="font-bold text-gray-800 text-sm truncate">{{ $comp->name }}</p>
                                                <p class="text-[11px] text-gray-500 leading-relaxed mt-0.5">{{ Str::limit($comp->description, 80) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10 border-2 border-dashed border-blue-100 rounded-2xl bg-blue-50/20">
                                    <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-microchip text-blue-300 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-600">No components yet</p>
                                    <p class="text-xs text-gray-400 mt-1">Add the hardware/software components your project needs.</p>
                                </div>
                            @endif
                        </div>
                        </div> {{-- End Components Collapsible Area --}}
                    </div>

                    {{-- 🟢 الجزء الثاني: المصروفات (Expenses - Outgoing) --}}
                    <div x-data="{ expanded: false }" class="bg-white rounded-[2.5rem] border border-gray-200 shadow-xl overflow-hidden hover-lift flex flex-col">
                        <div @click="expanded = !expanded" class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 cursor-pointer hover:bg-gray-100 transition-colors">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <div class="p-2 bg-red-50 rounded-lg text-red-500 shadow-sm"><i
                                        class="fas fa-shopping-cart"></i></div>
                                Expenses
                                <i class="fas fa-chevron-down text-sm text-gray-400 transition-transform duration-300 ml-1" :class="expanded ? 'rotate-180' : ''"></i>
                            </h3>
                            @if ($myRole == 'leader')
                                <button @click.stop onclick="openModal('addExpenseModal')"
                                    class="text-xs btn-gold px-4 py-2 rounded-xl font-bold shadow-md"><i
                                        class="fas fa-plus"></i>
                                    Add Item</button>
                            @endif
                        </div>

                        <div x-show="expanded" x-transition.opacity.duration.300ms style="display: none;" class="flex flex-col flex-grow">
                        <div class="p-6">
                            @php $totalSpent = $team->expenses->sum('amount'); @endphp
                            <div
                                class="bg-red-50 rounded-2xl p-4 mb-4 border border-red-100 flex justify-between items-center">
                                <span class="text-xs font-bold text-red-400 uppercase">Total Spent</span>
                                <span class="text-2xl font-black text-red-600">{{ number_format($totalSpent) }} <small
                                        class="text-sm font-medium">EGP</small></span>
                            </div>

                            {{-- جدول المصروفات المختصر --}}
                            <div class="overflow-y-auto max-h-60 custom-scroll">
                                <table class="w-full text-sm text-left">
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($team->expenses as $expense)
                                            <tr>
                                                <td class="py-3 px-2">
                                                    <div class="flex items-center gap-3">
                                                        @if ($expense->receipt_path)
                                                            <a href="{{ $expense->receipt_path }}"
                                                                target="_blank"
                                                                class="group relative block w-10 h-10 rounded-lg overflow-hidden border border-gray-200 hover:border-blue-400 transition">
                                                                <img src="{{ $expense->receipt_path }}"
                                                                    alt="Receipt"
                                                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                                                <div
                                                                    class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition">
                                                                </div>
                                                            </a>
                                                        @else
                                                            <div
                                                                class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-300 border border-gray-100">
                                                                <i class="fas fa-receipt text-xs"></i>
                                                            </div>
                                                        @endif

                                                        <div>
                                                            <p class="font-bold text-gray-800">
                                                                {{ $expense->item ?? $expense->item_name }}
                                                            </p>
                                                            <p class="text-[10px] text-gray-400">{{ $expense->shop_name }}</p>
                                                            @if (isset($expense->quantity) && $expense->quantity > 1)
                                                                <p class="text-[10px] text-blue-400">
                                                                    {{ number_format($expense->price_per_unit) }} EGP × {{ $expense->quantity }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="py-3 px-2 text-right font-bold text-red-500">
                                                    -{{ number_format($expense->amount) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center py-4 text-gray-400 text-xs">No
                                                    expenses yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </div> {{-- End Expenses Collapsible Area --}}
                    </div>
                    {{-- 🟡 الجزء الثالث: التمويل واللمّ (Funds Collection) --}}
                    <div x-data="{ expanded: false }" class="bg-white rounded-[2.5rem] border border-yellow-100 shadow-xl overflow-hidden hover-lift flex flex-col">
                        <div @click="expanded = !expanded" class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-[#FFF8E1]/50 cursor-pointer hover:bg-yellow-50 transition-colors">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <div class="p-2 bg-yellow-100 rounded-lg text-[#AA8A26] shadow-sm"><i
                                        class="fas fa-hand-holding-usd"></i></div>
                                Fund Collection
                                <i class="fas fa-chevron-down text-sm text-[#AA8A26] transition-transform duration-300 ml-1" :class="expanded ? 'rotate-180' : ''"></i>
                            </h3>
                            <div class="flex gap-2">
                                {{-- زرار الهستوري --}}
                                @if ($fundsHistory->count() > 0)
                                    <button @click.stop onclick="openModal('fundsHistoryModal')"
                                        class="text-xs bg-gray-100 text-gray-600 px-3 py-2 rounded-xl font-bold hover:bg-gray-200 transition"><i
                                            class="fas fa-history"></i> History</button>
                                @endif

                                @if ($myRole == 'leader')
                                    <button @click.stop onclick="openModal('createFundModal')"
                                        class="text-xs bg-black text-[#FFD700] px-4 py-2 rounded-xl font-bold shadow-md hover:gray-900 transition"><i
                                            class="fas fa-bullhorn"></i> Request</button>
                                @endif
                            </div>
                        </div>
                        <div x-show="expanded" x-transition.opacity.duration.300ms style="display: none;" class="flex flex-col flex-grow">
                        <div class="p-6 flex-grow">
                            @if ($activeFund)
                                {{-- Active Request Header --}}
                                <div class="mb-4">
                                    <div class="flex justify-between items-end mb-2">
                                        <div>
                                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Active:
                                                <span class="text-gray-800">{{ $activeFund->title }}</span>
                                            </p>
                                            <p class="text-xl font-black text-[#AA8A26]">
                                                {{ number_format($activeFund->amount_per_member) }} <small
                                                    class="text-gray-500 text-xs font-bold">EGP / Person</small>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-red-500 font-bold"><i class="far fa-clock"></i>
                                                Deadline
                                            </p>
                                            <p class="text-sm font-bold text-gray-800">
                                                {{ \Carbon\Carbon::parse($activeFund->deadline)->format('d M') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Progress Bar --}}
                                    @php
                                        $paidCount = $activeFund->contributions->where('status', 'paid')->count();
                                        $totalMembers = $team->members->count();
                                        $percent = $totalMembers > 0 ? ($paidCount / $totalMembers) * 100 : 0;
                                    @endphp
                                    <div class="bg-gray-100 rounded-full h-2 overflow-hidden mb-1">
                                        <div class="h-full bg-gradient-to-r from-yellow-400 to-[#AA8A26]"
                                            style="width: {{ $percent }}%"></div>
                                    </div>
                                    <p class="text-right text-[10px] text-gray-400">{{ $paidCount }} of
                                        {{ $totalMembers }}
                                        Paid</p>
                                </div>
                                {{-- Members List --}}
                                <div class="overflow-y-auto max-h-80 custom-scroll space-y-3">
                                    @foreach ($activeFund->contributions as $contrib)
                                        @php
                                            $debt = $membersDebts[$contrib->user_id] ?? 0;
                                            $isLate =
                                                $contrib->status == 'pending' &&
                                                \Carbon\Carbon::now()->gt($activeFund->deadline);
                                        @endphp

                                        <div
                                            class="flex items-center justify-between p-3 rounded-xl border {{ $contrib->status == 'paid' ? 'bg-green-50 border-green-100' : ($isLate ? 'bg-red-50 border-red-200' : 'bg-white border-gray-100') }}">
                                            <div class="flex items-center gap-3">
                                                <img class="w-9 h-9 rounded-full border shadow-sm"
                                                    src="https://ui-avatars.com/api/?name={{ $contrib->user->name }}">
                                                <div>
                                                    <p class="text-xs font-bold text-gray-800">{{ $contrib->user->name }}
                                                    </p>

                                                    {{-- 🔥 عرض الديون القديمة (الفضيحة) --}}
                                                    @if ($debt > 0)
                                                        <p
                                                            class="text-[9px] text-red-600 font-bold bg-red-100 px-1.5 rounded inline-block mt-0.5">
                                                            ⚠️ Owe: {{ number_format($debt) }} EGP (Old)
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                @if ($contrib->status == 'paid')
                                                    {{-- ✅ مدفوع (Paid) --}}
                                                    <div class="text-right">
                                                        <span
                                                            class="text-[10px] bg-green-100 text-green-700 px-2 py-1 rounded-lg font-bold block mb-0.5"><i
                                                                class="fas fa-check-circle"></i> Paid</span>
                                                        @if ($contrib->payment_method == 'transfer' && $contrib->payment_proof)
                                                            <a href="{{ $contrib->payment_proof }}"
                                                                target="_blank"
                                                                class="text-[9px] text-blue-500 underline">View Proof</a>
                                                        @elseif($contrib->payment_method == 'cash')
                                                            <span class="text-[9px] text-gray-400">Cash Payment</span>
                                                        @endif
                                                    </div>

                                                @elseif($contrib->status == 'pending_approval')
                                                    {{-- ⏳ قيد المراجعة (Pending Approval) --}}
                                                    @if ($myRole == 'leader')
                                                        {{-- Leader: Review Button --}}
                                                        <button
                                                            onclick="openReviewModal({{ json_encode([
                                                                'id' => $contrib->id,
                                                                'member_name' => $contrib->user->name,
                                                                'payment_method' => $contrib->payment_method,
                                                                'amount' => $contrib->amount ?? $activeFund->amount_per_member,
                                                                'from_number' => $contrib->from_number,
                                                                'transaction_date' => $contrib->transaction_date,
                                                                'transaction_time' => $contrib->transaction_time,
                                                                'proof_url' => $contrib->payment_proof ? $contrib->payment_proof : '#',
                                                                'notes' => $contrib->notes
                                                            ]) }})"
                                                            class="text-[10px] bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition font-bold shadow-md animate-pulse">
                                                            <i class="fas fa-search"></i> Review
                                                        </button>
                                                    @else
                                                        {{-- Member: Under Review --}}
                                                        <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-1 rounded-lg font-bold">
                                                            <i class="fas fa-clock"></i> Under Review
                                                        </span>
                                                    @endif

                                                @elseif($contrib->status == 'rejected')
                                                    {{-- ❌ مرفوض (Rejected) --}}
                                                    <div class="text-right">
                                                        <span class="text-[10px] bg-red-100 text-red-700 px-2 py-1 rounded-lg font-bold block mb-1">
                                                            <i class="fas fa-times-circle"></i> Rejected
                                                        </span>
                                                        @if ($contrib->user_id == Auth::id())
                                                            <button
                                                                onclick="openPaymentModal('{{ $contrib->id }}', '{{ $activeFund->amount_per_member }}')"
                                                                class="text-[10px] bg-gray-900 text-white px-3 py-1 rounded-lg hover:bg-gray-800 transition font-bold shadow-sm">
                                                                Try Again
                                                            </button>
                                                            @if($contrib->rejection_reason)
                                                                <p class="text-[9px] text-red-500 mt-1 max-w-[100px] leading-tight">{{ STR::limit($contrib->rejection_reason, 30) }}</p>
                                                            @endif
                                                        @endif
                                                    </div>

                                                @else
                                                    {{-- ⚪ لم يدفع (Pending) --}}
                                                    @if ($isLate)
                                                        <div class="text-right">
                                                            <span class="text-[10px] bg-red-600 text-white px-2 py-1 rounded-lg font-bold animate-pulse block mb-1">OVERDUE!</span>
                                                            @if ($contrib->user_id == Auth::id())
                                                                <button
                                                                    onclick="openPaymentModal('{{ $contrib->id }}', '{{ $activeFund->amount_per_member }}')"
                                                                    class="text-[10px] bg-gray-900 text-white px-3 py-1 rounded-lg hover:bg-[#D4AF37] transition font-bold shadow-md">
                                                                    Pay Now
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @elseif ($contrib->user_id == Auth::id())
                                                        {{-- Member: Pay Button --}}
                                                        <button
                                                            onclick="openPaymentModal('{{ $contrib->id }}', '{{ $activeFund->amount_per_member }}')"
                                                            class="text-[10px] bg-gray-900 text-white px-3 py-1.5 rounded-lg hover:bg-[#D4AF37] transition font-bold shadow-md">
                                                            Pay Now
                                                        </button>
                                                    @else
                                                        {{-- Leader: Just Pending --}}
                                                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-lg font-bold">Pending</span>
                                                    @endif
                                                @endif
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10 text-gray-400">
                                    <div
                                        class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-hand-holding-heart text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-bold">No active requests</p>
                                    <p class="text-xs">Create a fund request to start collecting.</p>
                                </div>
                            @endif
                        </div>
                        </div> {{-- End Fund Collection Collapsible Area --}}
                    </div>
                </div>
                </div> {{-- End Expenses & Budget Expanded Parent --}}
            </div>


            {{-- 4. معرض المشروع (Project Showroom) --}}
            <div x-data="{ expanded: false }" class="mt-12 mb-20">
                <div @click="expanded = !expanded" class="flex items-center justify-between mb-6 cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-purple-100 rounded-xl text-purple-600 shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fas fa-photo-video text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-800 flex items-center gap-2">
                            Project Showroom
                            <i class="fas fa-chevron-down text-lg text-gray-400 transition-transform duration-300 ml-2 group-hover:text-gray-600" :class="expanded ? 'rotate-180' : ''"></i>
                        </h3>
                    </div>
                    <button @click.stop onclick="openModal('uploadGalleryModal')"
                        class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg hover:bg-black transition transform hover:-translate-y-0.5 text-xs flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt"></i> Add Media
                    </button>
                </div>

                <div x-show="expanded" x-transition.opacity.duration.300ms style="display: none;"
                    class="bg-white rounded-[2.5rem] border border-gray-200 shadow-xl p-8 transition-all hover:shadow-2xl">
                    @php
                        $galleryItems = \Illuminate\Support\Facades\DB::table('project_galleries')
                            ->where('team_id', $team->id)
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp

                    @if ($galleryItems->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($galleryItems as $item)
                                {{-- كارت العرض --}}
                                <div
                                    class="group relative rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 bg-gray-50 h-64 cursor-pointer">

                                    {{-- 🟢 حالة 1: لو العنصر فيديو --}}
                                    @if (isset($item->type) && $item->type == 'video')
                                        <a href="{{ $item->video_link }}" target="_blank"
                                            class="block w-full h-full relative overflow-hidden">
                                            {{-- خلفية أنيميشن للفيديو --}}
                                            <div
                                                class="absolute inset-0 bg-gradient-to-br from-purple-600 via-blue-500 to-indigo-800 bg-size-200 animate-gradient-video">
                                            </div>

                                            {{-- زرار التشغيل --}}
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div
                                                    class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-white/50 group-hover:scale-110 transition duration-300 shadow-2xl">
                                                    <i class="fas fa-play text-white text-2xl ml-1"></i>
                                                </div>
                                                <div
                                                    class="absolute w-16 h-16 border-2 border-white/30 rounded-full animate-ping">
                                                </div>
                                            </div>
                                            <div
                                                class="absolute top-4 right-4 bg-black/50 text-white text-[10px] font-bold px-2 py-1 rounded backdrop-blur-sm">
                                                VIDEO
                                            </div>
                                        </a>

                                        {{-- 🟢 حالة 2: لو العنصر صورة --}}
                                    @else
                                        {{-- ✅ التعديل الأول: استخدام الراوت في Lightbox --}}
                                        <a href="{{ $item->file_path }}"
                                            data-lightbox="gallery" data-title="{{ $item->caption }}">

                                            {{-- ✅ التعديل الثاني: استخدام الراوت في src الصورة --}}
                                            <img src="{{ $item->file_path }}"
                                                class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                        </a>
                                    @endif

                                    {{-- طبقة المعلومات والأزرار (تظهر عند الهوفر) --}}
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-6 pointer-events-none">

                                        <span class="text-[10px] font-bold text-[#FFD700] uppercase tracking-wider mb-1">
                                            {{ $item->category ?? 'General' }}
                                        </span>
                                        <h4 class="text-white font-bold text-lg leading-tight truncate">
                                            {{ $item->caption }}
                                        </h4>

                                        <div class="flex justify-between items-center mt-3 pointer-events-auto">
                                            <span class="text-gray-300 text-xs flex items-center gap-1">
                                                <i class="fas fa-user-circle"></i>
                                                {{ \App\Models\User::find($item->user_id)->name ?? 'Member' }}
                                            </span>

                                            <div class="flex items-center gap-2">
                                                {{-- زرار التحميل (للجميع) --}}
                                                @if (isset($item->type) && $item->type == 'video')
                                                    <a href="{{ $item->video_link }}" target="_blank"
                                                        class="text-white hover:text-[#FFD700] bg-white/10 p-2 rounded-full backdrop-blur-md transition hover:bg-white/20"
                                                        title="Open Video">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @else
                                                    {{-- ✅ التعديل الثالث: استخدام الراوت في زرار التحميل --}}
                                                    <a href="{{ $item->file_path }}"
                                                        download
                                                        class="text-white hover:text-green-400 bg-white/10 p-2 rounded-full backdrop-blur-md transition hover:bg-white/20"
                                                        title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif

                                                {{-- زرار الحذف (لليدر فقط) --}}
                                                @if ($myRole == 'leader')
                                                    <form action="{{ route('final_project.deleteGallery', $item->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-red-400 hover:text-red-500 bg-white/10 p-2 rounded-full backdrop-blur-md transition hover:bg-white/20"
                                                            title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- حالة الفراغ --}}
                        <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50/50">
                            <div
                                class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm animate-bounce-slow">
                                <i class="fas fa-photo-video text-4xl text-gray-300"></i>
                            </div>
                            <h4 class="text-gray-800 font-bold text-lg">Empty Showroom</h4>
                            <p class="text-gray-500 text-sm mb-6">Share project images, diagrams, or demo videos.</p>
                            <button onclick="openModal('uploadGalleryModal')"
                                class="text-blue-600 font-bold text-xs hover:underline uppercase tracking-wide">
                                Start Uploading
                            </button>
                        </div>
                    @endif
                </div> {{-- End Showroom Body --}}
            </div>

            {{-- ستايل الأنيميشن الخاص بالفيديو --}}
            <style>
                .bg-size-200 {
                    background-size: 200% 200%;
                }

                .animate-gradient-video {
                    animation: gradientMove 3s ease infinite;
                }

                @keyframes gradientMove {
                    0% {
                        background-position: 0% 50%;
                    }

                    50% {
                        background-position: 100% 50%;
                    }

                    100% {
                        background-position: 0% 50%;
                    }
                }

                .animate-bounce-slow {
                    animation: bounce 2s infinite;
                }
            </style>

            {{-- الحاوية الرئيسية للـ Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10 items-start">
                {{-- العمود الأول: قسم التقارير الأسبوعية --}}
                <div id="reports-section" x-data="{ expanded: false }"
                    class="bg-white rounded-[2.5rem] border border-gray-200 shadow-xl overflow-hidden hover-lift transition-all flex flex-col">
                    <div @click="expanded = !expanded" class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 cursor-pointer hover:bg-gray-100 transition-colors">
                        <h3 class="font-bold text-gray-800 flex items-center gap-3 text-lg">
                            <div class="p-2 bg-blue-100 rounded-lg text-blue-600 shadow-sm"><i class="fas fa-stream"></i>
                            </div>
                            Progress Timeline
                            <i class="fas fa-chevron-down text-sm text-gray-400 transition-transform duration-300 ml-1" :class="expanded ? 'rotate-180' : ''"></i>
                        </h3>
                        @if (
                            $team &&
                                ($team->leader_id == auth()->id() ||
                                    $team->members->where('user_id', auth()->id())->first()?->extra_role == 'reports'))
                            <button @click.stop onclick="openModal('addReportModal')"
                                class="text-xs btn-royal-gold px-5 py-2.5 rounded-xl transition shadow-lg flex items-center gap-2 hover:-translate-y-0.5 transform font-bold">
                                <i class="fas fa-plus"></i> Weekly Report
                            </button>
                        @endif
                    </div>
                    <div x-show="expanded" x-transition.opacity.duration.300ms style="display: none;" class="flex flex-col flex-grow">
                    <div class="p-8 relative custom-scroll max-h-[500px] overflow-y-auto">
                        <div class="absolute left-12 top-8 bottom-8 w-0.5 bg-gray-100"></div>
                        @php
                            $reports = \Illuminate\Support\Facades\DB::table('weekly_reports')
                                ->where('team_id', $team->id)
                                ->orderBy('week_number', 'desc')
                                ->get();
                        @endphp
                        @forelse($reports as $report)
                            <div class="relative pl-12 mb-8 group">
                                <div
                                    class="absolute left-0 w-8 h-8 bg-white border-4 border-[#D4AF37] rounded-full flex items-center justify-center z-10 shadow-md group-hover:scale-110 transition">
                                    <span class="text-[10px] font-bold text-gray-700">{{ $report->week_number }}</span>
                                </div>
                                <div
                                    class="bg-white border border-gray-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition hover:border-[#D4AF37]/30 transform group-hover:translate-x-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm">Week {{ $report->week_number }}
                                                Report</h4>
                                            <span class="text-xs text-gray-400">
                                                By {{ \App\Models\User::find($report->user_id)->name }} •
                                                <span class="text-blue-500 font-bold"><i class="far fa-calendar-alt"></i>
                                                    {{ \Carbon\Carbon::parse($report->report_date)->format('d M, h:i A') }}</span>
                                            </span>
                                        </div>

                                        {{-- ✅✅✅ التعديل هنا: استخدام الراوت الجوكر لعرض الملف --}}
                                        @if ($report->file_path)
                                            <a href="{{ $report->file_path }}"
                                                target="_blank"
                                                class="text-xs text-blue-500 bg-blue-50 px-2 py-1 rounded-lg hover:bg-blue-100 transition border border-blue-100">
                                                <i class="fas fa-paperclip"></i> View File
                                            </a>
                                        @endif
                                        {{-- ✅✅✅ نهاية التعديل --}}

                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-[10px] font-bold text-green-600 uppercase tracking-wider mb-1">
                                                Achievements</p>
                                            <p class="text-xs text-gray-600 leading-relaxed">{{ $report->achievements }}
                                            </p>
                                        </div>
                                        <div class="flex gap-4">
                                            <div class="flex-1">
                                                <p
                                                    class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1">
                                                    Next Plan</p>
                                                <p class="text-xs text-gray-600 leading-relaxed">{{ $report->plans }}</p>
                                            </div>
                                            @if ($report->challenges)
                                                <div class="flex-1">
                                                    <p
                                                        class="text-[10px] font-bold text-red-500 uppercase tracking-wider mb-1">
                                                        Challenges</p>
                                                    <p class="text-xs text-gray-600 leading-relaxed">
                                                        {{ $report->challenges }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 pl-4">
                                <div
                                    class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-history text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-400 text-sm">No weekly reports submitted yet.</p>
                            </div>
                        @endforelse
                    </div>
                    </div> {{-- End Progress Timeline Body --}}
                </div>

                {{-- العمود الثاني: منطقة التسليم النهائي --}}
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-br from-[#D4AF37] to-[#8B7500] flex items-center justify-center shadow-lg animate-float">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-800">Final Submission</h2>
                            <p class="text-gray-500 text-sm">Upload final deliverables.</p>
                        </div>
                    </div>

                    <div
                        class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-[#D4AF37]/20 relative hover:border-[#D4AF37] transition duration-500">
                        <div
                            class="absolute top-0 right-0 w-64 h-64 bg-[#D4AF37]/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
                        </div>

                        <form action="{{ route('final_project.submit_final', $project->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="p-8 space-y-6 relative z-10">

                                {{-- 1. رفع الكتاب --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                        <i class="fas fa-book text-[#D4AF37] mr-1"></i> Project Book (Thesis)
                                    </label>
                                    @if ($team->final_book_file)
                                        <div
                                            class="flex items-center justify-between p-4 bg-green-50 rounded-xl border border-green-200">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                                <span class="text-sm font-bold text-gray-700">Project_Book.pdf</span>
                                            </div>
                                            {{-- ✅✅✅ التعديل الأول: استخدام الراوت الجوكر --}}
                                            <a href="{{ $team->final_book_file }}"
                                                target="_blank"
                                                class="text-green-600 hover:text-green-800 text-xs font-bold underline">Download</a>
                                        </div>
                                    @else
                                        @if (auth()->id() == $team->leader_id)
                                            <input type="file" name="final_book" accept=".pdf"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#D4AF37]/10 file:text-[#AA8A26] border-2 border-dashed border-gray-200 rounded-xl cursor-pointer bg-gray-50/50 p-2 hover:bg-gray-100 transition">
                                        @else
                                            <p class="text-sm text-gray-400 italic">Waiting for leader to upload...</p>
                                        @endif
                                    @endif
                                </div>

                                {{-- 2. رفع العرض التقديمي --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                        <i class="fas fa-desktop text-[#D4AF37] mr-1"></i> Final Presentation
                                    </label>
                                    @if ($team->presentation_file)
                                        <div
                                            class="flex items-center justify-between p-4 bg-green-50 rounded-xl border border-green-200">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-file-powerpoint text-orange-500 text-xl"></i>
                                                <span class="text-sm font-bold text-gray-700">Presentation.pptx</span>
                                            </div>
                                            {{-- ✅✅✅ التعديل الثاني: استخدام الراوت الجوكر --}}
                                            <a href="{{ $team->presentation_file }}"
                                                target="_blank"
                                                class="text-green-600 hover:text-green-800 text-xs font-bold underline">Download</a>
                                        </div>
                                    @else
                                        @if (auth()->id() == $team->leader_id)
                                            <input type="file" name="presentation" accept=".ppt,.pptx,.pdf"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#D4AF37]/10 file:text-[#AA8A26] border-2 border-dashed border-gray-200 rounded-xl cursor-pointer bg-gray-50/50 p-2 hover:bg-gray-100 transition">
                                        @else
                                            <p class="text-sm text-gray-400 italic">Waiting for leader to upload...</p>
                                        @endif
                                    @endif
                                </div>

                                {{-- 3. رابط فيديو المناقشة (ده لينك خارجي يوتيوب فمش محتاج تعديل) --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                        <i class="fab fa-youtube text-red-500 mr-1"></i> Defense Video Link
                                    </label>
                                    @if (auth()->id() == $team->leader_id)
                                        <input type="url" name="defense_video"
                                            value="{{ $team->defense_video_link }}" placeholder="https://..."
                                            class="w-full border-2 border-gray-100 bg-gray-50 p-4 rounded-xl focus:border-[#D4AF37] outline-none transition font-medium text-gray-700">
                                    @else
                                        <p class="text-sm text-gray-400 italic">Waiting for leader to upload...</p>
                                    @endif
                                </div>
                            </div>

                            @if (!$team->is_fully_submitted)
                                {{-- الحالة الأولى: لم يتم التسليم النهائي بعد --}}
                                @if (auth()->id() == $team->leader_id)
                                    {{-- (1) لو المستخدم هو الليدر: اعرض أزرار التحكم --}}
                                    <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex flex-col gap-4">
                                        {{-- زر التسليم النهائي --}}
                                        <button type="submit" name="submit_finish" value="1"
                                            onclick="return confirmAction(event, 'WARNING: This is the Final Submission. You cannot undo this action!')"
                                            class="w-full group relative px-8 py-4 rounded-xl font-bold text-white shadow-lg overflow-hidden transition-all transform hover:-translate-y-1"
                                            style="background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);">
                                            <span class="relative z-10 flex items-center justify-center gap-2">
                                                Final Submit <i class="fas fa-flag-checkered text-[#FFD700]"></i>
                                            </span>
                                        </button>

                                        {{-- زر حفظ المسودة --}}
                                        <button type="submit"
                                            class="text-gray-500 font-bold hover:text-gray-700 text-sm transition text-center hover:underline flex items-center justify-center gap-1">
                                            <i class="fas fa-save"></i> Save Draft
                                        </button>
                                    </div>
                                @else
                                    {{-- (2) لو المستخدم عضو عادي: اعرض رسالة انتظار فقط --}}
                                    <div class="bg-yellow-50 px-8 py-6 border-t border-yellow-100 text-center">
                                        <div
                                            class="text-yellow-700 font-medium text-sm flex items-center justify-center gap-2">
                                            <i class="fas fa-user-clock"></i>
                                            <span>Waiting for leader to submit</span>
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{-- الحالة الثانية: تم التسليم النهائي (القفل) --}}
                                <div class="bg-green-500/10 px-8 py-6 border-t border-green-500/20 text-center">
                                    <p class="text-green-700 font-bold flex items-center justify-center gap-2">
                                        <i class="fas fa-lock"></i> Project Submitted
                                    </p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <br>

            {{-- كارت ميعاد المناقشة والنتيجة --}}
            <div>
                @if (isset($team) && $team)
                    <div>
                        @if ($team->defense_date)
                            <div
                                class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl shadow-lg p-6 mb-8 text-white relative overflow-hidden transform hover:-translate-y-1 transition">
                                <div class="flex justify-between items-center relative z-10">
                                    <div>
                                        <h2 class="text-2xl font-bold mb-1">📅 Defense Scheduled</h2>
                                        <p class="text-purple-100">Get ready to present your work!</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-3xl font-black">
                                            {{ \Carbon\Carbon::parse($team->defense_date)->format('d M') }}
                                        </p>
                                        <p class="text-sm font-bold bg-white/20 px-3 py-1 rounded inline-block mt-1">
                                            {{ \Carbon\Carbon::parse($team->defense_date)->format('h:i A') }}
                                        </p>
                                        <p class="text-sm mt-2 opacity-90"><i class="fas fa-map-marker-alt"></i>
                                            {{ $team->defense_location }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div
                                class="flex flex-col items-center justify-center h-48 relative z-10 text-center bg-gray-50 rounded-2xl mb-8 border border-gray-100">
                                <div
                                    class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 animate-pulse shadow-sm">
                                    <i class="fas fa-hourglass-half text-gray-500 text-2xl"></i>
                                </div>
                                <h4 class="text-gray-500 font-bold text-sm">Not Scheduled Yet for project discussion
                                </h4>
                                <p class="text-gray-400 text-xs mt-1 max-w-[200px]">Your supervisor hasn't set the
                                    defense
                                    date.</p>
                            </div>
                        @endif
                    </div>

                    {{-- 🎓 كارت النتيجة للطالب --}}
                    <div
                        class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden border border-gray-100 h-250 transition-all hover:shadow-2xl hover:border-purple-200">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-purple-50 rounded-full blur-xl"></div>
                        <h3
                            class="text-sm font-bold uppercase tracking-widest mb-6 flex items-center gap-2 relative z-10 text-gray-800 border-b border-gray-100 pb-4">
                            <i class="fas fa-graduation-cap text-purple-600"></i> Final Result
                        </h3>
                        @if (isset($team) && $team->project_score)
                            @php
                                $percentage = ($team->project_score / $team->project_max_score) * 100;
                                $color = $percentage >= 50 ? 'text-green-600' : 'text-red-600';
                                $statusText = $percentage >= 50 ? 'PASSED' : 'FAILED';
                                $statusBg =
                                    $percentage >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                            @endphp
                            <div class="text-center relative z-10 animate-fade-in">
                                <span
                                    class="inline-block px-3 py-1 rounded-full {{ $statusBg }} text-[10px] font-bold uppercase mb-4 tracking-wider shadow-sm">{{ $statusText }}</span>
                                <div class="flex flex-col items-center justify-center mb-4">
                                    <span
                                        class="text-5xl font-black text-gray-800 tracking-tighter">{{ $team->project_score }}</span>
                                    <span class="text-gray-400 font-bold text-sm uppercase mt-1">Out of
                                        {{ $team->project_max_score }}</span>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                    <p class="text-xs text-gray-500 font-bold uppercase mb-1">Percentage</p>
                                    <p class="text-2xl font-bold {{ $color }}">{{ round($percentage, 1) }}%
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-40 relative z-10 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-clipboard-list text-gray-300 text-2xl"></i>
                                </div>
                                <h4 class="text-gray-400 font-bold text-sm">Results Pending</h4>
                                <p class="text-gray-400 text-[10px] mt-1">Your final grade hasn't been released yet.
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @elseif($team)
            <div
                class="mt-12 bg-white rounded-[2.5rem] border border-gray-200 shadow-xl p-12 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gray-50/50"></div>
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-gray-100 rounded-full blur-3xl opacity-50"></div>
                <div class="relative z-10">
                    <div
                        class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg border-4 border-gray-50">
                        <i class="fas fa-lock text-5xl text-gray-300"></i>
                    </div>
                    <h3 class="text-3xl font-black text-gray-800 mb-2">Workspace Locked</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-8 text-sm leading-relaxed">
                        Operations (Meetings, Tasks, Budget, Reports) are locked until your project proposal is
                        <span class="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded">Approved</span>.
                    </p>
                    @if ($team->proposal_status == 'rejected')
                        <div
                            class="inline-block bg-red-50 text-red-600 px-8 py-4 rounded-2xl font-bold border border-red-100 shadow-sm animate-pulse">
                            <i class="fas fa-exclamation-circle mr-2"></i> Proposal Rejected - Please Resubmit
                        </div>
                    @elseif($team->proposal_status == 'pending')
                        <div
                            class="inline-block bg-yellow-50 text-yellow-600 px-8 py-4 rounded-2xl font-bold border border-yellow-100 shadow-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-hourglass-half animate-spin-slow"></i>
                                <span>Waiting for Supervisor Approval...</span>
                            </div>
                        </div>
                    @else
                        <div
                            class="inline-block bg-gray-100 text-gray-500 px-8 py-4 rounded-2xl font-bold border border-gray-200">
                            <i class="fas fa-paper-plane mr-2"></i> Please Submit your proposal to unlock.
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>


    {{-- ========================================== --}}
    {{-- 🔩 مودال إضافة مكون (Add Component Modal) --}}
    {{-- ========================================== --}}
    <div id="addComponentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal('addComponentModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-microchip text-white text-lg"></i>
                        </div>
                        <h3 class="text-white font-bold text-lg">Add Component</h3>
                    </div>
                    <button onclick="closeModal('addComponentModal')" class="text-white/70 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('final_project.storeComponent') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id }}">

                    {{-- Component Name --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Component Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition"
                            placeholder="e.g. Arduino Uno, Raspberry Pi, LCD Screen...">
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" required rows="3"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition resize-none"
                            placeholder="How will this component help in the project?"></textarea>
                    </div>

                    {{-- Component Image --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Component Image <span class="text-gray-400">(optional)</span></label>
                        <div class="relative border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 transition cursor-pointer" onclick="document.getElementById('comp_image_input').click()">
                            <i class="fas fa-image text-gray-300 text-2xl mb-1"></i>
                            <p class="text-xs text-gray-400" id="comp_image_label">Click to upload image</p>
                            <input type="file" id="comp_image_input" name="image" accept="image/*" class="hidden"
                                onchange="document.getElementById('comp_image_label').innerText = this.files[0]?.name || 'Click to upload image'">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeModal('addComponentModal')"
                            class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-bold transition shadow-lg">
                            <i class="fas fa-check mr-1"></i> Accept
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 💸 مودال إضافة مصروف (Add Expense Modal) --}}
    {{-- ========================================== --}}
    <div id="addExpenseModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal('addExpenseModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-red-500 to-rose-500 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white text-lg"></i>
                        </div>
                        <h3 class="text-white font-bold text-lg">Add Expense</h3>
                    </div>
                    <button onclick="closeModal('addExpenseModal')" class="text-white/70 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('final_project.storeExpense') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
                    @csrf
                    <input type="hidden" name="team_id" value="{{ $team->id }}">

                    {{-- Select Component --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Select Component <span class="text-red-500">*</span></label>
                        @if ($components->count() > 0)
                            <select name="component_id" id="expense_component_select" required
                                onchange="expenseUpdateTotal()"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 outline-none transition bg-white">
                                <option value="">-- Choose a component --</option>
                                @foreach ($components as $comp)
                                    <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 text-sm text-yellow-700 font-bold">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                No components added yet. Please add components first from the Components card.
                            </div>
                            {{-- Disable submit if no components --}}
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const form = document.querySelector('#addExpenseModal form');
                                    if (form) {
                                        const submitBtn = form.querySelector('button[type="submit"]');
                                        if (submitBtn) submitBtn.disabled = true;
                                    }
                                });
                            </script>
                        @endif
                    </div>

                    {{-- Unit Price --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Unit Price (EGP) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_per_unit" id="expense_unit_price" required min="0.01" step="0.01"
                            oninput="expenseUpdateTotal()"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 outline-none transition"
                            placeholder="e.g. 200">
                    </div>

                    {{-- Quantity +/- Control --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Quantity <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="expenseChangeQty(-1)"
                                class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-xl flex items-center justify-center transition select-none">−</button>
                            <input type="number" name="quantity" id="expense_qty" value="1" min="1" readonly
                                class="w-20 text-center border border-gray-200 rounded-xl py-2 text-base font-bold text-gray-800 focus:outline-none">
                            <button type="button" onclick="expenseChangeQty(1)"
                                class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-black text-xl flex items-center justify-center transition select-none">+</button>
                            {{-- Auto-calculated total --}}
                            <div class="flex-1 bg-red-50 border border-red-100 rounded-xl px-4 py-2 text-right">
                                <p class="text-[10px] text-red-400 font-bold uppercase">Total</p>
                                <p class="text-lg font-black text-red-600" id="expense_total_display">0 EGP</p>
                            </div>
                        </div>
                    </div>

                    {{-- Store Name --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Store Name <span class="text-red-500">*</span></label>
                        <input type="text" name="shop_name" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 outline-none transition"
                            placeholder="e.g. Maadi Electronics, Amazon...">
                    </div>

                    {{-- Receipt Image --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Receipt Image <span class="text-gray-400">(optional)</span></label>
                        <div class="relative border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-red-300 transition cursor-pointer" onclick="document.getElementById('expense_receipt_input').click()">
                            <i class="fas fa-receipt text-gray-300 text-2xl mb-1"></i>
                            <p class="text-xs text-gray-400" id="expense_receipt_label">Click to upload receipt</p>
                            <input type="file" id="expense_receipt_input" name="receipt" accept="image/*" class="hidden"
                                onchange="document.getElementById('expense_receipt_label').innerText = this.files[0]?.name || 'Click to upload receipt'">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeModal('addExpenseModal')"
                            class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit"
                            class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl text-sm font-bold transition shadow-lg">
                            <i class="fas fa-check mr-1"></i> Save Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function expenseChangeQty(delta) {
            const qtyInput = document.getElementById('expense_qty');
            let val = parseInt(qtyInput.value) || 1;
            val = Math.max(1, val + delta);
            qtyInput.value = val;
            expenseUpdateTotal();
        }

        function expenseUpdateTotal() {
            const price = parseFloat(document.getElementById('expense_unit_price')?.value) || 0;
            const qty   = parseInt(document.getElementById('expense_qty')?.value) || 1;
            const total = price * qty;
            const display = document.getElementById('expense_total_display');
            if (display) display.innerText = total.toLocaleString('en-EG') + ' EGP';
        }
    </script>

    {{-- ========================================== --}}
    {{-- 🛑 مودال تقديم الدفع (Payment Submission Modal) --}}
    {{-- ========================================== --}}
    <div id="paymentSubmissionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal('paymentSubmissionModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <form action="{{ route('final_project.submitPayment') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="contribution_id" id="submit_contribution_id">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-money-bill-wave text-yellow-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Submit Payment
                                </h3>
                                <div class="mt-4 space-y-4">
                                    {{-- Payment Method --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                        <select name="payment_method" id="payment_method_select" onchange="togglePaymentFields(this.value)"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm rounded-md">
                                            <option value="transfer">Vodafone Cash / InstaPay</option>
                                            <option value="cash">Cash (Hand to Hand)</option>
                                        </select>
                                    </div>

                                    {{-- Transfer Fields --}}
                                    <div id="transfer_fields" class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Amount Transferred (EGP)</label>
                                            <input type="number" step="0.01" name="amount_transferred" id="default_amount_input"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Transfer From (Number)</label>
                                            <input type="text" name="from_number" placeholder="010xxxxxxxx"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                        </div>
                                         <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Date</label>
                                                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Time</label>
                                                <input type="time" name="transaction_time" value="{{ date('H:i') }}"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Screenshot (Proof)</label>
                                            <input type="file" name="proof_image" accept="image/*"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                                        </div>
                                    </div>

                                    {{-- Cash Fields --}}
                                    <div id="cash_fields" class="hidden space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Notes (Required)</label>
                                            <textarea name="notes" rows="3" placeholder="I gave you the money when we met at..."
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Submit Payment
                        </button>
                        <button type="button" onclick="closeModal('paymentSubmissionModal')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ========================================== --}}
    {{-- 🛑 مودال مراجعة الدفع (Payment Review Modal) --}}
    {{-- ========================================== --}}
    <div id="paymentReviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal('paymentReviewModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <form action="{{ route('final_project.reviewPayment') }}" method="POST">
                    @csrf
                    <input type="hidden" name="contribution_id" id="review_contribution_id">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-search-dollar text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Review Payment Request
                                </h3>
                                
                                {{-- Details Display Area --}}
                                <div class="mt-4 bg-gray-50 p-4 rounded-lg space-y-2 text-sm">
                                    <p><strong>Member:</strong> <span id="review_member_name">Loading...</span></p>
                                    <p><strong>Method:</strong> <span id="review_method" class="uppercase">Loading...</span></p>
                                    
                                    {{-- Transfer Details --}}
                                    <div id="review_transfer_details" class="hidden">
                                        <p><strong>Amount:</strong> <span id="review_amount" class="text-green-600 font-black"></span> EGP</p>
                                        <p><strong>From:</strong> <span id="review_from"></span></p>
                                        <p><strong>Date:</strong> <span id="review_date"></span></p>
                                        <div class="mt-2">
                                            <a id="review_proof_link" href="#" target="_blank" class="text-blue-500 underline text-xs font-bold">
                                                <i class="fas fa-image"></i> View Screenshot
                                            </a>
                                        </div>
                                    </div>
                                    
                                    {{-- Cash Details --}}
                                    <div id="review_cash_details" class="hidden">
                                        <p class="mt-1"><strong>Notes:</strong></p>
                                        <p id="review_notes" class="text-gray-600 italic border-l-2 border-gray-300 pl-2"></p>
                                    </div>
                                </div>

                                {{-- Rejection Reason (Conditional) --}}
                                <div id="rejection_area" class="mt-4 hidden animate-fade-in-up">
                                    <label class="block text-sm font-medium text-red-700">Rejection Reason</label>
                                    <textarea name="rejection_reason" id="rejection_reason_input" rows="2" 
                                        class="mt-1 block w-full border border-red-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                        placeholder="Why is it rejected?"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        {{-- Approve Button --}}
                        <button type="submit" name="action" value="approve" id="approve_btn"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:w-auto sm:text-sm">
                            <i class="fas fa-check mr-2 mt-1"></i> Confirm Payment
                        </button>
                        
                        {{-- Reject Button --}}
                        <button type="button" onclick="showRejectionField()" id="reject_btn"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:w-auto sm:text-sm">
                            <i class="fas fa-times mr-2 mt-1"></i> Reject
                        </button>

                         {{-- Confirm Reject Button (Hidden initially) --}}
                         <button type="submit" name="action" value="reject" id="confirm_reject_btn" class="hidden w-full justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-800 text-base font-medium text-white hover:bg-red-900 focus:outline-none sm:w-auto sm:text-sm">
                            Confirm Rejection
                        </button>

                        <button type="button" onclick="closeModal('paymentReviewModal')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePaymentFields(method) {
            if (method === 'transfer') {
                document.getElementById('transfer_fields').classList.remove('hidden');
                document.getElementById('cash_fields').classList.add('hidden');
            } else {
                document.getElementById('transfer_fields').classList.add('hidden');
                document.getElementById('cash_fields').classList.remove('hidden');
            }
        }

        function openPaymentModal(contributionId, amount) {
            document.getElementById('submit_contribution_id').value = contributionId;
            document.getElementById('default_amount_input').value = amount;
            openModal('paymentSubmissionModal');
        }

        function openReviewModal(data) {
            // Data is an object with all details
            document.getElementById('review_contribution_id').value = data.id;
            document.getElementById('review_member_name').innerText = data.member_name;
            document.getElementById('review_method').innerText = data.payment_method;

            // Reset UI
            document.getElementById('review_transfer_details').classList.add('hidden');
            document.getElementById('review_cash_details').classList.add('hidden');
            document.getElementById('rejection_area').classList.add('hidden');
            document.getElementById('approve_btn').classList.remove('hidden');
            document.getElementById('reject_btn').classList.remove('hidden');
             document.getElementById('confirm_reject_btn').classList.add('hidden');

            if (data.payment_method === 'transfer') {
                document.getElementById('review_transfer_details').classList.remove('hidden');
                document.getElementById('review_amount').innerText = data.amount;
                document.getElementById('review_from').innerText = data.from_number;
                document.getElementById('review_date').innerText = data.transaction_date + ' at ' + data.transaction_time;
                document.getElementById('review_proof_link').href = data.proof_url;
            } else {
                document.getElementById('review_cash_details').classList.remove('hidden');
                document.getElementById('review_notes').innerText = data.notes;
            }

            openModal('paymentReviewModal');
        }

        function showRejectionField() {
            document.getElementById('rejection_area').classList.remove('hidden');
            document.getElementById('approve_btn').classList.add('hidden');
            document.getElementById('reject_btn').classList.add('hidden');
            const confirmBtn = document.getElementById('confirm_reject_btn');
            confirmBtn.classList.remove('hidden');
            confirmBtn.classList.add('inline-flex');
            document.getElementById('rejection_reason_input').required = true;
        }
    </script>

@endsection

{{-- ========================================================= --}}
{{-- 👑 The Logic Hub (Enhanced JavaScript) --}}
{{-- ========================================================= --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    // 1. Preloader Logic
    window.addEventListener('load', function() {
        const preloader = document.getElementById('royal-preloader');
        preloader.style.opacity = '0';
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 500);
    });

    // 2. Modal Functions (Standard with Fixes)
    // 🛠️ التعديل الجوهري هنا: إضافة الكلاس royal-modal-active
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('hidden');
            // 🔥 إضافة كلاسات الإصلاح
            el.classList.add('royal-modal-active');
            // 🔥 منع سكرول الخلفية
            document.body.classList.add('modal-open');

            // إضافة أنيميشن للمحتوى الداخلي إذا وجد
            const content = el.querySelector('div[class*="bg-white"]');
            if (content) {
                content.classList.add('modal-content-styled');
            }
        }
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('hidden');
            // 🔥 إزالة كلاسات الإصلاح
            el.classList.remove('royal-modal-active');
            // 🔥 إرجاع سكرول الخلفية
            document.body.classList.remove('modal-open');
        }
    }

    // 3. Confirm Action with SweetAlert2
    function confirmAction(e, message) {
        e.preventDefault();
        const form = e.target.closest('form'); // Catch closest form
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, proceed!',
            background: '#fff url("https://www.transparenttextures.com/patterns/cubes.png")',
        }).then((result) => {
            if (result.isConfirmed) {
                if (form) form.submit();
            }
        });
        return false;
    }

    // 4. Copy Code with Toast
    function copyCode(code) {
        navigator.clipboard.writeText(code);
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        Toast.fire({
            icon: 'success',
            title: 'Access Code Copied!'
        });
    }

    // 5. Specific Modal Handlers
    function openReportModal(userId, userName) {
        document.getElementById('reported_user_id_input').value = userId;
        document.getElementById('reported_member_name').innerText = userName;
        openModal('reportMemberModal'); // تم التعديل لاستخدام الدالة المحسنة
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            ['createTeamModal', 'joinTeamModal', 'inviteMemberModal', 'leaveTeamModal', 'reportMemberModal',
                'addTaskModal', 'addExpenseModal'
            ].forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.classList.contains('hidden')) closeModal(id);
            });
        }
    });

    function openManageModal(userId, userName, role, techRole, extraRole) {
        document.getElementById('manageUserId').value = userId;
        document.getElementById('manageUserName').innerText = userName;
        const roleRadios = document.getElementsByName('role');
        roleRadios.forEach(r => {
            if (r.value == role) r.checked = true;
        });
        const techRadios = document.getElementsByName('technical_role');
        techRadios.forEach(r => {
            if (r.value == (techRole || 'general')) r.checked = true;
        });
        const extraSelect = document.getElementById('manageExtraRole');
        if (extraSelect) extraSelect.value = extraRole || 'none';
        openModal('manageMemberModal');
    }

    function openMarkPaidModal(contribId, userName, amount) {
        document.getElementById('paidContribId').value = contribId;
        document.getElementById('paidUserName').innerText = userName;
        document.getElementById('paidAmount').innerText = amount + ' EGP';
        openModal('markPaidModal');
    }

    function openAttendanceModal(id, topic) {
        document.getElementById('attendanceMeetingId').value = id;
        document.getElementById('attendanceMeetingTopic').innerText = topic;
        window.dispatchEvent(new CustomEvent('load-attendees', { detail: '' }));
        openModal('markAttendanceModal');
    }

    const teamMembers = @json(isset($team) && $team
            ? $team->members->map(function ($m) {
                return ['id' => $m->user->id, 'name' => $m->user->name, 'tech' => $m->technical_role];
            })
            : []
    );

    function openAddTaskModal(type) {
        const select = document.getElementById('taskAssignUser');
        select.innerHTML = '';
        const filtered = teamMembers.filter(m => m.tech === type);
        filtered.forEach(m => {
            let option = document.createElement('option');
            option.value = m.id;
            option.text = m.name;
            select.appendChild(option);
        });
        const header = document.getElementById('addTaskHeader');
        const badge = document.getElementById('addTaskTypeBadge');
        if (type === 'software') {
            header.className = 'bg-blue-600 px-8 py-5 transition-colors';
            badge.innerText = 'Software';
            badge.className = 'bg-white/20 text-white text-[10px] px-2 py-0.5 rounded ml-2 uppercase';
        } else {
            header.className = 'bg-orange-600 px-8 py-5 transition-colors';
            badge.innerText = 'Hardware';
            badge.className = 'bg-white/20 text-white text-[10px] px-2 py-0.5 rounded ml-2 uppercase';
        }
        openModal('addTaskModal');
    }

    function openSubmitTaskModal(taskId, title) {
        document.getElementById('submitTaskTitle').innerText = title;
        document.getElementById('submitTaskForm').action = "/tasks/" + taskId + "/submit";
        openModal('submitTaskModal');
    }

    // 6. Visual Effects
    document.addEventListener('click', function(e) {
        const ripple = document.createElement('div');
        ripple.className = 'gold-ripple';
        document.body.appendChild(ripple);
        const size = 20;
        ripple.style.left = (e.pageX - size / 2) + 'px';
        ripple.style.top = (e.pageY - size / 2) + 'px';
        ripple.style.width = size + 'px';
        ripple.style.height = size + 'px';
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });

    const mouseGlow = document.getElementById('mouse-glow');
    document.addEventListener('mousemove', (e) => {
        mouseGlow.style.left = e.clientX + 'px';
        mouseGlow.style.top = e.clientY + 'px';
    });

    const tiltCards = document.querySelectorAll('.tilt-effect');
    tiltCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -5;
            const rotateY = ((x - centerX) / centerX) * 5;
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg)`;
        });
    });

    // 7. Chart.js Visualization for Budget
    @if (isset($team) && $team->expenses->count() > 0)
        const ctx = document.getElementById('expensesChart').getContext('2d');
        const expensesData = @json(
            $team->expenses->groupBy('item')->map(function ($row) {
                return $row->sum('amount');
            }));

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(expensesData),
                datasets: [{
                    label: 'Expenses (EGP)',
                    data: Object.values(expensesData),
                    backgroundColor: [
                        '#FFD700', '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    @endif
</script>

{{-- Export Excel Modal (Team Leader Only) --}}
@if(auth()->user()->email === '2420823@batechu.com')
<div id="exportMembersModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('exportMembersModal')"></div>
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md mx-4 relative z-10 overflow-hidden transform transition-all p-8" x-data="{
        exporting: false,
        columns: ['name', 'academic_number', 'national_id', 'email', 'phone_number', 'whatsapp_number', 'address', 'role']
    }">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-black text-gray-800 flex items-center gap-3">
                <i class="fas fa-file-excel text-green-500"></i> Export Excel
            </h3>
            <button onclick="closeModal('exportMembersModal')" class="text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('final_project.export_members') }}" method="POST" @submit="exporting = true; setTimeout(() => exporting = false, 3000)">
            @csrf
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <input type="hidden" name="group_id" value="all" id="exportGroupId">

            <p class="text-sm border-l-4 border-green-500 bg-green-50 p-3 rounded text-green-800 mb-6 font-semibold" id="exportGroupText">Exporting entire team.</p>

            <div class="space-y-3 mb-8">
                <label class="font-bold text-gray-700 block mb-2">Select Columns to Export:</label>
                <div class="grid grid-cols-2 gap-3">
                    <template x-for="item in [{id: 'name', label: 'Name'}, {id: 'academic_number', label: 'Academic Number'}, {id: 'national_id', label: 'National ID'}, {id: 'email', label: 'Email'}, {id: 'phone_number', label: 'Phone Number'}, {id: 'whatsapp_number', label: 'WhatsApp Number'}, {id: 'address', label: 'Address'}, {id: 'role', label: 'Role'}]">
                        <label class="flex items-center gap-2 cursor-pointer group bg-gray-50 p-2 rounded-lg border border-gray-100 hover:border-green-200 transition-colors">
                            <input type="checkbox" name="columns[]" :value="item.id" x-model="columns" class="w-4 h-4 text-green-500 bg-white border-gray-300 rounded focus:ring-green-500">
                            <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition truncate" x-text="item.label" :title="item.label"></span>
                        </label>
                    </template>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="button" onclick="closeModal('exportMembersModal')" class="flex-1 px-4 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-green-500 text-white rounded-xl font-bold shadow-lg shadow-green-500/30 hover:bg-green-600 transition flex items-center justify-center gap-2" :disabled="exporting || columns.length === 0" :class="exporting || columns.length === 0 ? 'opacity-70 cursor-not-allowed' : ''">
                    <i class="fas fa-spinner fa-spin" x-show="exporting"></i>
                    <i class="fas fa-download" x-show="!exporting"></i>
                    <span x-text="exporting ? 'Exporting...' : 'Export'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openExportModal(groupId) {
        document.getElementById('exportGroupId').value = groupId;
        document.getElementById('exportGroupText').innerText = 
            groupId === 'all' ? 'Exporting ALL Team Members.' : 'Exporting Group ' + groupId + ' Members ONLY.';
        openModal('exportMembersModal');
    }
</script>
@endif

<script>
    function openMarkPaidModal(contribId, amount, userName) {
        // 1. نحط البيانات في المودال
        document.getElementById('paidContribId').value = contribId;
        document.getElementById('paidAmount').innerText = amount + ' EGP';
        document.getElementById('paidUserName').innerText = userName;

        // 2. نقفل مودال الهيستوري (عشان الزحمة)
        closeModal('fundsHistoryModal');

        // 3. نفتح مودال الدفع
        openModal('markPaidModal');
    }
</script>
