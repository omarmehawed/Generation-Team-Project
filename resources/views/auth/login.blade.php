<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Generation Team - Login</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { primary: '#2596be' }
                }
            }
        }
    </script>
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
            opacity: 1;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.4); /* Light Overlay */
            backdrop-filter: blur(5px);
            z-index: 0;
        }

        .glass-card-light {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .input-light {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            color: #1e293b;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .input-light:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .btn-light-premium {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            font-weight: 700;
            font-family: 'Orbitron', sans-serif;
            padding: 1rem 2rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            letter-spacing: 0.1em;
            box-shadow: 0 10px 20px rgba(2, 132, 199, 0.2);
        }

        .btn-light-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(2, 132, 199, 0.3);
            filter: brightness(1.05);
        }
    </style>
</head>

<body class="h-screen flex items-center justify-center relative overflow-hidden text-slate-900" style="background: #f8fafc;">

    {{-- 🎨 INCLUDE THEME ENGINE (FORCE LIGHT) 🎨 --}}
    @include('partials.theme')

    {{-- 🎥 LIVE VIDEO WALLPAPER (WITH LIGHT OVERLAY) 🎥 --}}
    <div class="video-container">
        <video autoplay muted loop playsinline class="video-bg" data-src="{{ asset('assets/videos/login_bg.mp4') }}">
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
                    class="absolute inset-0 bg-blue-500/10 blur-2xl rounded-full group-hover:bg-blue-400/20 transition-all duration-500">
                </div>
                <img src="{{ asset('assets/gt_logo.jpg') }}" alt="Generation Team Logo"
                    class="w-full h-full object-cover rounded-2xl relative z-10 border border-white/50 shadow-xl group-hover:scale-105 transition-transform duration-500">
            </div>

            <h1
                class="text-6xl font-tech font-bold text-slate-900 mb-2 relative z-10">
                GENERATION
            </h1>
            <h2 class="text-4xl font-tech font-light tracking-[0.3em] text-slate-700 mb-6 relative z-10">
                TEAM
            </h2>

            <div class="h-1.5 w-24 bg-blue-500 mb-6 relative z-10 rounded-full"></div>

            <p class="text-slate-600 text-lg leading-relaxed font-semibold relative z-10 uppercase tracking-wide">
                Access the future of project management. <br>
                Secure. Efficient. Advanced.
            </p>
        </div>

        {{-- Right Side: Login Form --}}
        <div class="w-full max-w-md animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="glass-card-light p-8 sm:p-10 relative overflow-hidden group">

                {{-- Mobile Logo --}}
                <div class="lg:hidden flex justify-center mb-8">
                    <img src="{{ asset('assets/gt_logo.jpg') }}" alt="Logo"
                        class="w-20 h-20 rounded-xl shadow-lg">
                </div>

                <div class="mb-8 relative z-10">
                    <h3 class="text-2xl font-tech font-bold text-slate-900 mb-1">SYSTEM LOGIN</h3>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Initialization Protocol Required</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6 relative z-10">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            <i class="fas fa-id-card mr-1 text-blue-500"></i> Access ID (Email)
                        </label>
                        <input id="email" name="email" type="email" required autofocus value="{{ old('email') }}"
                            class="input-light font-semibold"
                            placeholder="user@generation.team">
                        @error('email')
                            <p class="text-red-500 text-[10px] mt-1 font-bold"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            <i class="fas fa-lock mr-1 text-blue-500"></i> Security Key (Password)
                        </label>
                        <div class="relative group">
                            <input id="password" name="password" type="password" required
                                class="input-light font-semibold pr-12"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword()" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-blue-500 transition-colors">
                                <i id="passwordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-[10px] mt-1 font-bold"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-[11px] font-bold text-slate-500 hover:text-slate-700 cursor-pointer transition-colors">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 mr-2 border-slate-300 rounded text-blue-600 focus:ring-blue-500">
                            Persistent Session
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full btn-light-premium flex items-center justify-center gap-3">
                        <span>INITIALIZE</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>

                    <div class="relative flex items-center justify-center w-full my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative px-4 text-[10px] font-black text-slate-400 bg-white/50 backdrop-blur-sm rounded-full">OR</div>
                    </div>

                    <a href="{{ route('auth.google') }}"
                        class="w-full flex items-center justify-center gap-3 py-3.5 text-xs font-black bg-white dark:bg-gray-800 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-all shadow-sm active:scale-95">
                        <img src="https://www.gstatic.com/images/branding/product/1x/googleg_48dp.png" alt="Google" class="w-4 h-4">
                        <span class="tracking-widest capitalize">Sign in with Google</span>
                    </a>

                </form>
            </div>

            <p class="text-center text-slate-400 text-[10px] mt-8 font-black tracking-[0.2em] uppercase">
                Secure Link // End-to-End Encryption
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const videos = document.querySelectorAll('video[data-src]');
            videos.forEach(function (video) {
                const source = document.createElement('source');
                source.setAttribute('src', video.getAttribute('data-src'));
                source.setAttribute('type', 'video/mp4');
                video.appendChild(source);
                video.load();
                video.play().catch(e => console.log('Autoplay prevented', e));
            });
        });
    </script>
</body>

</html>