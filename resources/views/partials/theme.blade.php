<style>
    /* 
       🎨 GENERATION TEAM - ULTIMATE THEME ENGINE 🎨
       Centralized Design System for Light/Dark Modes & Animations
    */

    :root {
        /* Default Light Mode (Clean Modern) */
        --bg-main: #f8fafc;
        --bg-panel: #ffffff;
        --bg-sidebar: #ffffff;
        --bg-header: rgba(255, 255, 255, 0.90);
        --text-main: #0f172a;
        --text-muted: #64748b;
        --primary: #0284c7;
        /* Professional Blue */
        --primary-hover: #0369a1;
        --accent: #0ea5e9;
        --border: #e2e8f0;
        --input-bg: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-glow: 0 0 0 transparent;
        --grid-color: rgba(148, 163, 184, 0.1);
        --orb-opacity: 0.3;
        --overlay-bg: rgba(255, 255, 255, 0.5);
    }

    [data-theme="dark"] {
        /* Premium Dark Mode (Deep Space) */
        --bg-main: #0b0f19;
        /* Deepest Blue/Black */
        --bg-panel: #151b2b;
        /* Elevated Surface */
        --bg-sidebar: #0f172a;
        /* Sidebar specific */
        --bg-header: rgba(11, 15, 25, 0.85);
        --text-main: #f1f5f9;
        /* Soft White */
        --text-muted: #94a3b8;
        /* Slate 400 */
        --primary: #00f3ff;
        /* Brand Cyan Neon */
        --primary-hover: #22d3ee;
        --accent: #8b5cf6;
        /* Violet Secondary */
        --border: #1e293b;
        /* Subtle Border */
        --input-bg: #1e293b;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.3);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.5);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.5);
        --shadow-glow: 0 0 20px rgba(0, 243, 255, 0.15);
        /* Neon Glow */
        --grid-color: rgba(0, 243, 255, 0.05);
        --orb-opacity: 0.15;
        --overlay-bg: rgba(11, 15, 25, 0.7);
    }

    /* 🌊 GLOBAL RESET & TYPOGRAPHY 🌊 */
    body {
        font-family: 'Rajdhani', sans-serif;
        background-color: var(--bg-main);
        color: var(--text-main);
        transition: color 0.3s ease, background-color 0.3s ease;
        overflow-x: hidden;
        /* Prevent horizontal scroll from animations */
        -webkit-font-smoothing: antialiased;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-family: 'Orbitron', sans-serif;
        letter-spacing: 0.05em;
    }

    /* 🌀 LIVE ANIMATED BACKGROUND 🌀 */
    .live-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: -1;
        overflow: hidden;
        pointer-events: none;
        background: var(--bg-main);
        transition: background 0.5s ease;
    }

    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: var(--orb-opacity);
        animation: float 20s infinite ease-in-out;
        transition: opacity 0.5s ease;
    }

    .orb-1 {
        width: 50vw;
        height: 50vw;
        background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
        top: -10%;
        left: -10%;
        animation-delay: 0s;
    }

    .orb-2 {
        width: 40vw;
        height: 40vw;
        background: radial-gradient(circle, var(--accent) 0%, transparent 70%);
        bottom: -10%;
        right: -10%;
        animation-delay: -5s;
    }

    .orb-3 {
        width: 30vw;
        height: 30vw;
        background: radial-gradient(circle, var(--primary-hover) 0%, transparent 70%);
        top: 40%;
        left: 40%;
        animation-delay: -10s;
    }

    .tech-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(to right, var(--grid-color) 1px, transparent 1px),
            linear-gradient(to bottom, var(--grid-color) 1px, transparent 1px);
        background-size: 40px 40px;
        mask-image: radial-gradient(circle at center, black 40%, transparent 100%);
        -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 100%);
        opacity: 0.5;
    }

    @keyframes float {

        0%,
        100% {
            transform: translate(0, 0) scale(1);
        }

        33% {
            transform: translate(30px, -50px) scale(1.1);
        }

        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }
    }

    /* 💎 PREMIUM COMPONENTS 💎 */

    /* Glass Panels */
    .glass-card {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        backdrop-filter: blur(12px);
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-2px);
        border-color: var(--primary);
        box-shadow: var(--shadow-lg), var(--shadow-glow);
    }

    /* Buttons */
    .btn-premium {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
        color: #000;
        /* Contrast for Neon */
        font-weight: 700;
        font-family: 'Orbitron', sans-serif;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        position: relative;
        overflow: hidden;
    }

    [data-theme="light"] .btn-premium {
        color: #fff;
    }

    .btn-premium:hover {
        box-shadow: 0 0 20px var(--primary);
        transform: translateY(-1px);
        filter: brightness(1.1);
    }

    .btn-premium:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text-muted);
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        border-color: var(--text-main);
        color: var(--text-main);
        background: rgba(255, 255, 255, 0.05);
    }

    /* Inputs */
    .input-premium {
        background-color: var(--input-bg);
        border: 1px solid var(--border);
        color: var(--text-main);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .input-premium:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 243, 255, 0.1);
    }

    /* Tables */
    .table-premium {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-premium th {
        background-color: var(--bg-main);
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .table-premium td {
        background-color: var(--bg-panel);
        color: var(--text-main);
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        transition: background-color 0.2s ease;
    }

    .table-premium tr:hover td {
        background-color: rgba(0, 243, 255, 0.02);
    }

    [data-theme="light"] .table-premium tr:hover td {
        background-color: #f1f5f9;
    }

    /* Global Overrides for Legacy Classes */
    [data-theme="dark"] .bg-white {
        background-color: var(--bg-panel) !important;
        color: var(--text-main) !important;
        border-color: var(--border) !important;
    }

    [data-theme="dark"] .text-gray-900 {
        color: var(--text-main) !important;
    }

    [data-theme="dark"] .text-gray-500 {
        color: var(--text-muted) !important;
    }

    [data-theme="dark"] .border-gray-200 {
        border-color: var(--border) !important;
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: var(--bg-main);
    }

    ::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--text-muted);
    }

    /* 🚀 PAGE LOADER 🚀 */
    #global-loader {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: var(--bg-main);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    .loader-logo {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        margin-bottom: 2rem;
        animation: pulse-glow 2s infinite ease-in-out;
    }

    .loader-bar {
        width: 200px;
        height: 4px;
        background: var(--border);
        border-radius: 2px;
        overflow: hidden;
        position: relative;
    }

    .loader-progress {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 30%;
        background: var(--primary);
        border-radius: 2px;
        animation: loading-bar 1.5s infinite ease-in-out;
    }

    @keyframes pulse-glow {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 0 20px transparent;
        }

        50% {
            transform: scale(1.05);
            box-shadow: 0 0 40px var(--primary);
            border-color: var(--primary);
        }
    }

    @keyframes loading-bar {
        0% {
            left: -30%;
        }

        50% {
            left: 40%;
            width: 50%;
        }

        100% {
            left: 100%;
            width: 10%;
        }
    }

    /* Utility Helpers */
    .text-glow {
        text-shadow: 0 0 10px var(--primary);
    }

    .border-glow {
        box-shadow: 0 0 10px var(--primary);
        border-color: var(--primary);
    }
</style>

<!-- Live Background Element -->
<div class="live-background">
    <div class="tech-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<!-- Global Loader Element -->
<div id="global-loader">
    <img src="{{ asset('assets/gt_logo.jpg') }}" class="loader-logo shadow-lg" alt="Loading...">
    <div class="loader-bar">
        <div class="loader-progress"></div>
    </div>
    <p class="mt-4 font-tech text-sm tracking-widest opacity-50" style="color: var(--text-muted)">SYSTEM INITIALIZING...
    </p>
</div>

<script>
    // Remove Loader on Page Load
    window.addEventListener('load', function () {
        const loader = document.getElementById('global-loader');
        setTimeout(() => {
            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';
        }, 800); // Slight delay for smoothness
    });

    // Theme Persistence Script for Inline Use
    if (localStorage.getItem('theme') === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
    } else {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
</script>