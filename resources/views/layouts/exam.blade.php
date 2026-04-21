<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Environment - {{ config('app.name', 'Generation Team') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/gt_logo.jpg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8fafc; /* Very light gray */
            -webkit-user-select: none; /* Safari */
            -ms-user-select: none; /* IE 10 and IE 11 */
            user-select: none; /* Standard syntax */
        }
        /* Allow selection inside inputs/textareas */
        input, textarea, .allow-select {
            -webkit-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }

        /* Gold Gradient Button */
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
    </style>
    @stack('styles')
</head>
<body class="antialiased h-screen overflow-hidden flex flex-col">
    <!-- Top Locked Header -->
    <header class="bg-gray-900 border-b-4 border-yellow-500 py-3 px-6 shrink-0 z-50 shadow-xl">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                <i class="fas fa-shield-alt text-yellow-500 text-2xl animate-pulse"></i>
                <span class="text-white font-black tracking-widest uppercase">Secure Exam Environment</span>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-gray-300 font-bold text-sm hidden sm:block">
                    Candidate: <span class="text-white">{{ auth()->user()->name }}</span>
                </div>
                <!-- Dynamic Timer Slot -->
                @yield('timer')
            </div>
        </div>
    </header>

    <!-- Main Content Area (Scrollable) -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8" id="exam-scroll-area">
        @yield('content')
    </main>

    <!-- Global Disable Context Menu -->
    <script>
        document.addEventListener('contextmenu', event => event.preventDefault());
    </script>
    
    @stack('scripts')
</body>
</html>
