<!DOCTYPE html>
<html lang="en" x-data="{ theme: localStorage.getItem('theme') || 'light' }" :class="{'dark': theme === 'dark' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Generation Team</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Figtree:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --bg-main: #f8fafc;
            --text-main: #1e1b4b;
            --text-muted: #475569;
            --primary: #1e1b4b;
            --accent: #2596be;
            --glow: 0 10px 40px -10px rgba(30, 27, 75, 0.3);
            --overlay: rgba(248, 250, 252, 0.7);
        }

        .dark {
            --bg-main: #010413;
            --text-main: #f8fafc;
            --text-muted: #cbd5e1;
            --primary: #2596be;
            --accent: #818cf8;
            --glow: 0 0 60px rgba(251, 191, 36, 0.3);
            --overlay: rgba(1, 4, 19, 0.75);
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: all 0.3s ease;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        /* 🖼️ Sharp Branded Wallpaper Background */
        @keyframes drift {
            0% { transform: scale(1.05) translate(0, 0); }
            50% { transform: scale(1.12) translate(-1.5%, -1%); }
            100% { transform: scale(1.05) translate(0, 0); }
        }

        .error-bg {
            position: fixed;
            inset: -8%;
            z-index: -3;
            background-image: url("{{ asset('assets/gt_logo_glow.png') }}");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            filter: contrast(1.1) brightness(0.9);
            animation: drift 35s ease-in-out infinite;
        }

        /* 🌑 Sharp Transparent Overlay */
        .error-overlay {
            position: fixed;
            inset: 0;
            z-index: -2;
            background-color: var(--overlay);
        }

        /* ✨ Lively Animated Orbs */
        @keyframes float-orb {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.2; }
            33% { transform: translate(60px, -80px) scale(1.2); opacity: 0.4; }
            66% { transform: translate(-40px, 60px) scale(0.85); opacity: 0.3; }
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            z-index: -1;
            filter: blur(100px);
            animation: float-orb 20s ease-in-out infinite;
        }

        .font-tech { font-family: 'Orbitron', sans-serif; }

        /* Subtle Tech-Grid Layer */
        .tech-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            opacity: 0.08;
            background-image: radial-gradient(var(--text-muted) 1px, transparent 1px);
            background-size: 80px 80px;
            pointer-events: none;
        }

        .content-wrap {
            position: relative;
            z-index: 10;
            padding: 2rem;
            max-width: 800px;
            animation: fadeInScale 1.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95) translateY(40px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .code-display {
            font-size: clamp(8rem, 20vw, 12rem);
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.05em;
            color: var(--primary);
            text-shadow: 0 20px 80px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            position: relative;
        }

        .code-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            opacity: 0.2;
            filter: blur(40px);
            color: var(--primary);
        }

        .status-text {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.4em;
            margin-bottom: 1.5rem;
            color: var(--text-main);
        }

        .message-text {
            font-size: 1.125rem;
            max-width: 500px;
            margin: 0 auto 3rem;
            color: var(--text-muted);
            font-weight: 500;
            line-height: 1.8;
        }

        .btn-home {
            background: linear-gradient(135deg, var(--primary), #4f46e5);
            color: white;
            padding: 1.25rem 3.5rem;
            border-radius: 2rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
            border: 2px solid transparent;
        }

        .dark .btn-home {
            background: linear-gradient(135deg, var(--primary), #1c7ca0);
            color: #1e1b4b;
        }

        .btn-home:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: var(--glow);
            border-color: rgba(255,255,255,0.2);
        }

        /* Responsive */
        @media (max-width: 640px) {
            .status-text { letter-spacing: 0.2em; }
            .message-text { font-size: 1rem; }
        }
    </style>
</head>
<body>
    <div class="error-bg"></div>
    <div class="error-overlay"></div>
    
    <!-- Animated Neon Layer -->
    <div class="orb w-[600px] h-[600px] -top-60 -left-60" style="background: var(--primary); animation-delay: 0s;"></div>
    <div class="orb w-[500px] h-[500px] bottom-[-10%] right-[-10%]" style="background: var(--accent); animation-delay: -7s; opacity: 0.2;"></div>

    <div class="tech-grid"></div>

    <div class="content-wrap">
        <!-- Floating Branded Header -->
        <div class="mb-8 opacity-40 flex flex-col items-center gap-1">
            <span class="text-[12px] uppercase tracking-[0.8em] font-tech text-[var(--text-muted)]">Generation Team System</span>
            <div class="w-24 h-0.5 bg-[var(--primary)] rounded-full opacity-20"></div>
        </div>

        <div class="code-display font-tech">
            @yield('code')
            <div class="code-glow">@yield('code')</div>
        </div>
        
        <h2 class="status-text font-tech">@yield('status')</h2>
        
        <p class="message-text">@yield('message')</p>

        <!-- Home Button -->
        <a href="/" class="btn-home">
            <span class="text-sm">Initiate Re-entry</span>
            <i class="fas fa-chevron-right text-xs opacity-50"></i>
        </a>

        <!-- Theme Switcher (Mini) -->
        <div class="fixed bottom-12 left-1/2 -translate-x-1/2 flex justify-center items-center gap-8 text-[var(--text-muted)]">
             <button @click="theme = 'light'; localStorage.setItem('theme', 'light')" 
                     :class="theme ==='light' ? 'text-[var(--primary)] scale-125' : 'hover:scale-110'"
                     class="transition-all duration-300">
                <i class="fas fa-sun text-xl"></i>
             </button>
             <div class="w-1.5 h-1.5 rounded-full bg-[var(--text-muted)] opacity-30"></div>
             <button @click="theme = 'dark'; localStorage.setItem('theme', 'dark')" 
                     :class="theme ==='dark' ? 'text-[var(--primary)] scale-125' : 'hover:scale-110'"
                     class="transition-all duration-300">
                <i class="fas fa-moon text-xl"></i>
             </button>
        </div>
    </div>
</body>
</html>
