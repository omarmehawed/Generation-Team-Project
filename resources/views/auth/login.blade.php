<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Generation Team - Login</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;500;700&display=swap"
        rel="stylesheet">
    <style>
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

        /* 🎥 VIDEO BACKGROUND 🎥 */
        .video-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .video-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
            opacity: 0.6;
            /* Slight transparency for text readability */
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Darken video */
            z-index: 0;
        }
    </style>
</head>

<body class="h-screen flex items-center justify-center relative overflow-hidden text-white" style="background: #000;">

    {{-- 🎨 INCLUDE THEME ENGINE (Background, Styles, Loader) 🎨 --}}
    @include('partials.theme')

    {{-- 🎥 LIVE VIDEO WALLPAPER 🎥 --}}
    <div class="video-container">
        <video autoplay muted loop playsinline class="video-bg">
            <source src="{{ asset('assets/videos/login_bg.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="video-overlay"></div>
    </div>

    <div
        class="container mx-auto px-4 relative z-10 flex flex-col lg:flex-row items-center justify-center h-full gap-12 lg:gap-24">

        {{-- Left Side: Branding (Visible on Desktop) --}}
        <div
            class="hidden lg:flex flex-col items-center lg:items-start text-center lg:text-left max-w-lg animate-fade-in-up p-12">

            <div class="w-32 h-32 mb-8 relative group z-10">
                <div
                    class="absolute inset-0 bg-cyan-500/30 blur-2xl rounded-full group-hover:bg-cyan-400/50 transition-all duration-500">
                </div>
                <img src="{{ asset('assets/gt_logo.jpg') }}" alt="Generation Team Logo"
                    class="w-full h-full object-cover rounded-2xl relative z-10 border border-white/20 shadow-[0_0_30px_rgba(0,243,255,0.3)] group-hover:scale-105 transition-transform duration-500">
            </div>

            <h1
                class="text-6xl font-tech font-bold text-transparent bg-clip-text bg-gradient-to-r from-white to-cyan-200 mb-2 drop-shadow-[0_0_15px_rgba(0,243,255,1)] relative z-10">
                GENERATION
            </h1>
            <h2 class="text-4xl font-tech font-light tracking-[0.3em] text-white/90 mb-6 relative z-10 drop-shadow-lg">
                TEAM
            </h2>

            <div class="h-1 w-24 bg-gradient-to-r from-cyan-400 to-transparent mb-6 relative z-10"></div>

            <p class="text-white/90 text-lg leading-relaxed font-light relative z-10 drop-shadow-md">
                Access the future of project management. <br>
                Secure. Efficient. Advanced.
            </p>
        </div>

        {{-- Right Side: Login Form --}}
        <div class="w-full max-w-md animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="glass-card p-8 sm:p-10 relative overflow-hidden group">

                {{-- Decorative Glow --}}
                <div
                    class="absolute -top-10 -right-10 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl group-hover:bg-cyan-500/20 transition-all duration-500">
                </div>

                {{-- Mobile Logo --}}
                <div class="lg:hidden flex justify-center mb-8">
                    <img src="{{ asset('assets/gt_logo.jpg') }}" alt="Logo"
                        class="w-20 h-20 rounded-xl shadow-[0_0_20px_rgba(0,243,255,0.4)]">
                </div>

                <div class="mb-8 relative z-10">
                    <h3 class="text-2xl font-tech font-bold text-white mb-2">SYSTEM LOGIN</h3>
                    <p class="text-cyan-200/50 text-sm">Enter your credentials to initialize session.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6 relative z-10">
                    @csrf

                    {{-- Email --}}
                    <div class="group/input">
                        <label class="block text-xs font-bold text-cyan-300 uppercase tracking-wider mb-2">
                            <i class="fas fa-id-card mr-2"></i> Access ID (Email)
                        </label>
                        <input id="email" name="email" type="email" required autofocus value="{{ old('email') }}"
                            class="input-premium bg-slate-900/50 border-slate-700 text-white focus:border-cyan-500 placeholder-slate-600"
                            placeholder="user@system.com">
                        @error('email')
                            <p class="text-red-400 text-xs mt-1 font-mono"><i class="fas fa-exclamation-circle"></i>
                                {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="group/input">
                        <label class="block text-xs font-bold text-cyan-300 uppercase tracking-wider mb-2">
                            <i class="fas fa-lock mr-2"></i> Security Key (Password)
                        </label>
                        <input id="password" name="password" type="password" required
                            class="input-premium bg-slate-900/50 border-slate-700 text-white focus:border-cyan-500 placeholder-slate-600"
                            placeholder="••••••••">
                        @error('password')
                            <p class="text-red-400 text-xs mt-1 font-mono"><i class="fas fa-exclamation-circle"></i>
                                {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between text-xs">
                        <label
                            class="flex items-center text-cyan-200/60 hover:text-cyan-100 cursor-pointer transition-colors">
                            <input type="checkbox" name="remember"
                                class="mr-2 bg-transparent border-cyan-500/30 rounded focus:ring-offset-0 focus:ring-cyan-500 text-cyan-500">
                            Keep Session Active
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full btn-premium flex items-center justify-center gap-3 py-4 text-base tracking-widest hover:scale-[1.02] active:scale-[0.98]">
                        <span>INITIALIZE</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>

                </form>
            </div>

            <p class="text-center text-cyan-900/40 text-[10px] mt-6 font-mono tracking-widest">
                SYSTEM SECURE // ENCRYPTED CONNECTION
            </p>
        </div>
    </div>

    <style>
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

</body>

</html>